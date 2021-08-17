<?php
/**
 * User: cfn <cfn@leapy.cn>
 * Datetime: 2021/8/15 23:22
 * Copyright: php
 */

namespace unionpay\MiniProgram\access;

use unionpay\Kernel\Contracts\AccessTokenInterface;
use unionpay\Kernel\Events\AccessTokenRefreshed;
use unionpay\Kernel\ServiceContainer;
use unionpay\Kernel\Traits\HasHttpRequests;
use unionpay\Kernel\Traits\InteractsWithCache;

/**
 * Class AccessToken
 * @package unionpay\MiniProgram\access
 */
class AccessToken implements AccessTokenInterface
{
    use HasHttpRequests;
    use InteractsWithCache;

    /**
     * @var ServiceContainer
     */
    protected $app;

    /**
     * @var string
     */
    protected $requestMethod = 'POST';

    /**
     * @var string
     */
    protected $endpointToPostToken = "https://open.95516.com/open/access/1.0/token";

    /**
     * @var array
     */
    protected $token;

    /**
     * @var string
     */
    protected $tokenKey = 'accessToken';

    /**
     * @var string
     */
    protected $cachePrefix = 'unionpay.miniprogram.access.access_token.';

    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var string
     */
    protected $code = "";

    /**
     * AccessToken constructor.
     *
     * @param ServiceContainer $app
     */
    public function __construct(ServiceContainer $app)
    {
        $this->app = $app;
        $this->config = $app['config']->toArray();
    }

    /**
     * @param string $code
     * @param false $refresh
     * @return array|mixed
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 13:42
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getToken($code = "", $refresh = false)
    {
        $this->code = $code;
        $cacheKey = $this->getCacheKey();
        $cache = $this->getCache();

        $cacheItem = $cache->getItem($cacheKey);

        if (!$refresh && $cacheItem->isHit() && $result = $cacheItem->get()) {
            return $result;
        }

        $token = $this->requestToken($this->getCredentials());

        $this->setToken($token[$this->tokenKey], $token['openId'], $token['expiresIn'] ?: 7200);

        $this->app->events->dispatch(new AccessTokenRefreshed($this));

        return $token;
    }

    /**
     * @param string $code
     * @return array|mixed|AccessTokenInterface
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 13:43
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getRefreshedToken($code = "")
    {
        return $this->getToken($code,true);
    }

    /**
     * @param array $token
     * @param int $lifetime
     * @return $this
     * @throws \Exception|\Psr\Cache\InvalidArgumentException
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 10:35
     */
    protected function setToken($token, $openId, $lifetime = 7200)
    {
        $cacheKey = $this->getCacheKey();
        $cache = $this->getCache();

        $cacheItem = $cache->getItem($cacheKey);
        $cacheItem->expiresAfter($lifetime);

        $cacheItem->set(array(
            $this->tokenKey => $token,
            'expiresIn' => $lifetime
        ));

        // 保存
        $cache->save($cacheItem);

        // 保存code缓存
        $this->setCacheCode($openId, $lifetime);

        return $this;
    }

    /**
     * code缓存起来
     * @param $openId
     * @param $lifetime
     * @return $this
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 19:58
     * @throws \Psr\Cache\InvalidArgumentException
     */
    protected function setCacheCode($openId, $lifetime)
    {
        $cache = $this->getCache();

        $cacheItem = $cache->getItem(md5($openId));
        $cacheItem->expiresAfter($lifetime);

        $cacheItem->set(array(
            'code' => $this->code,
            'expiresIn' => $lifetime
        ));

        // 保存
        $cache->save($cacheItem);

        return $this;
    }

    /**
     * @return string
     */
    protected function getCacheKey()
    {
        return $this->cachePrefix.md5(json_encode($this->getCredentials()));
    }

    /**
     * @return array
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 11:21
     */
    protected function getCredentials()
    {
        return [
            'appId' => $this->config['appid'],
            'backendToken' => $this->app->backend_token->getToken()['backendToken'],
            'code' => $this->code,
            'grantType' => 'authorization_code',
        ];
    }
}
