<?php
/**
 * User: cfn <cfn@leapy.cn>
 * Datetime: 2021/8/15 23:22
 * Copyright: php
 */

namespace unionpay\MiniProgram\access;

use HttpException;
use unionpay\Kernel\Contracts\FrontTokenInterface;
use unionpay\Kernel\Events\FrontTokenRefreshed;
use unionpay\Kernel\ServiceContainer;
use unionpay\Kernel\Support\Str;
use unionpay\Kernel\Traits\HasHttpRequests;
use unionpay\Kernel\Traits\InteractsWithCache;

/**
 * Class FrontToken
 * @package unionpay\MiniProgram\access
 */
class FrontToken implements FrontTokenInterface
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
    protected $endpointToPostToken = "https://open.95516.com/open/access/1.0/frontToken";

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
    protected $tokenKey = 'frontToken';

    /**
     * @var string
     */
    protected $cachePrefix = 'unionpay.miniprogram.access.front_token.';

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
     * @param false $refresh
     * @return array|mixed
     * @throws HttpException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\Cache\InvalidArgumentException
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 11:34
     */
    public function getToken($refresh = false)
    {
        $cacheKey = $this->getCacheKey();
        $cache = $this->getCache();

        $cacheItem = $cache->getItem($cacheKey);

        if (!$refresh && $cacheItem->isHit() && $result = $cacheItem->get()) {
            return $result;
        }

        $token = $this->requestToken($this->getCredentials());

        $this->setToken($token[$this->tokenKey], $token['expiresIn'] ?: 7200);

        $this->app->events->dispatch(new FrontTokenRefreshed($this));

        return $token;
    }

    /**
     * @return array|mixed|\unionpay\Kernel\Contracts\FrontTokenInterface
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 11:36
     * @throws HttpException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getRefreshedToken()
    {
        return $this->getToken(true);
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
            throw new HttpException('Request front_token fail: '.json_encode($result, JSON_UNESCAPED_UNICODE), $response, $result);
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
     * @date 2021/8/16 11:31
     * @throws HttpException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\Cache\InvalidArgumentException
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
        return $this->cachePrefix.md5(json_encode($this->config));
    }

    /**
     * @return array
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 11:21
     */
    protected function getCredentials()
    {
        $nonceStr = Str::nonceStr();
        $timestamp = time();
        return [
            'appId' => $this->config['appid'],
            'nonceStr' => $nonceStr,
            'timestamp' => $timestamp,
            'signature' => Str::signature($this->config['appid'],$nonceStr,$this->config['secret'],$timestamp)
        ];
    }
}
