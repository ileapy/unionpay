<?php
/**
 * User: cfn <cfn@leapy.cn>
 * Datetime: 2021/8/16 19:09
 * Copyright: php
 */

namespace unionpay\MiniProgram\user;

use unionpay\Kernel\ServiceContainer;
use unionpay\Kernel\Traits\HasHttpRequests;
use unionpay\Kernel\Traits\InteractsWithCache;

/**
 * Class Mobile
 *
 * @package unionpay\MiniProgram\user
 */
class Mobile
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
    protected $endpoint = "https://open.95516.com/open/access/1.0/user.mobile";

    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var string
     */
    protected $openId = "";

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
     * @param string $openId openid
     * @param bool $decrypt 是否解密返回
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\Cache\InvalidArgumentException
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 19:23
     */
    public function getMobile($openId, $decrypt = true)
    {
        $this->openId = $openId;

        $data = $this->requestToken($this->getCredentials());

        if (!isset($data['mobile'])) throw new \Exception('获取手机号失败，返回值为空');

        // 解密返回
        if ($decrypt)
            foreach ($data as $k => $v)
                if ($v)
                    $data[$k] = $this->app->crypto->decrypt($data[$k]);

        return $data;
    }

    /**
     * @param string $openId
     * @return mixed
     * @throws \Psr\Cache\InvalidArgumentException
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 20:01
     */
    public function getCode($openId)
    {
        $cache = $this->getCache();

        $cacheItem = $cache->getItem(md5($openId));

        return $cacheItem->get();
    }

    /**
     * @return array
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Exception
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 19:23
     */
    protected function getCredentials()
    {
        $code = $this->getCode($this->openId)['code'];
        if (!$code) throw new \Exception("code已失效，请重新授权获取");

        return [
            'appId' => $this->config['appid'],
            'accessToken' => $this->app->access_token->getToken($code)['accessToken'],
            'openId' => $this->openId,
            'backendToken' => $this->app->backend_token->getToken()['backendToken']
        ];
    }
}
