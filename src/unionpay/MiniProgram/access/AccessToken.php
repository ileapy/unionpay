<?php
/**
 * User: cfn <cfn@leapy.cn>
 * Datetime: 2021/8/15 23:22
 * Copyright: php
 */

namespace unionpay\MiniProgram\access;

use HttpException;
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
     * @var string
     */
    protected $queryName;

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
     * @date 2021/8/16 11:28
     * @throws HttpException
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

        $this->setToken($token[$this->tokenKey], $token['expiresIn'] ?: 7200);

        $this->app->events->dispatch(new AccessTokenRefreshed($this));

        return $token;
    }

    /**
     * @param string $code
     * @return array|mixed|AccessTokenInterface
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 11:28
     * @throws HttpException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getRefreshedToken($code = "")
    {
        return $this->getToken($code,true);
    }

    /**
     * @param string $token
     * @param int $lifetime
     * @return $this
     * @throws \Exception
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 10:35
     */
    public function setToken($token, $lifetime = 7200)
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

        return $this;
    }

    /**
     * @param array $credentials
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException|HttpException
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 10:06
     */
    public function requestToken(array $credentials)
    {
        $response = $this->sendRequest($credentials);
        $result = json_decode($response->getBody()->getContents(), true);

        if (empty($result) || !isset($result['resp']) || $result['resp'] != "00" || !isset($result['params'])) {
            throw new HttpException('Request access_token fail: '.json_encode($result, JSON_UNESCAPED_UNICODE), $response, $result);
        }

        return $result['params'];
    }

    /**
     * Send http request.
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function sendRequest(array $credentials)
    {
        $options = [
            ('GET' === $this->requestMethod) ? 'query' : 'json' => $credentials,
        ];
        return $this->setHttpClient($this->app['http_client'])->request($this->getEndpoint(), $this->requestMethod, $options);
    }

    /**
     * @return array
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 10:04
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    protected function getQuery()
    {
        return [$this->queryName ?: $this->tokenKey => $this->getToken()[$this->tokenKey]];
    }

    /**
     * @return mixed
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 11:21
     * @throws \Exception
     */
    public function getEndpoint()
    {
        if (empty($this->endpointToPostToken)) {
            throw new \Exception('No endpoint request.');
        }

        return $this->endpointToPostToken;
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
