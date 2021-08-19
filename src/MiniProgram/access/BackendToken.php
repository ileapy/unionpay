<?php
/**
 * User: cfn <cfn@leapy.cn>
 * Datetime: 2021/8/15 23:22
 * Copyright: php
 */

namespace unionpay\MiniProgram\access;

use unionpay\Kernel\Client\MiniProgramClient;
use unionpay\Kernel\Contracts\BackendTokenInterface;
use unionpay\Kernel\Events\BackendTokenRefreshed;
use unionpay\Kernel\Support\Str;

class BackendToken extends MiniProgramClient implements BackendTokenInterface
{
    /**
     * @var string
     */
    protected $endpoint = "https://open.95516.com/open/access/1.0/backendToken";

    /**
     * @var array
     */
    protected $token;

    /**
     * @var string
     */
    protected $tokenKey = 'backendToken';

    /**
     * @var string
     */
    protected $cachePrefix = 'unionpay.miniprogram.access.backend_token.';

    /**
     * @param false $refresh
     * @return array|mixed
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 19:20
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\Cache\InvalidArgumentException
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

        $this->app->events->dispatch(new BackendTokenRefreshed($this));

        return $token;

    }

    /**
     * @return array|mixed|\unionpay\Kernel\Contracts\AccessTokenInterface
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 19:20
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
     * @throws \Exception|\Psr\Cache\InvalidArgumentException
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 10:35
     */
    protected function setToken($token, $lifetime = 7200)
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
     * @return string
     */
    protected function getCacheKey()
    {
        return $this->cachePrefix.md5(json_encode($this->config));
    }

    /**
     * @return array
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 10:29
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
