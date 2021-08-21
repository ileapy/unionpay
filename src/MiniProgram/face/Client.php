<?php
/**
 * User: cfn <cfn@leapy.cn>
 * Datetime: 2021/8/20 23:45
 * Copyright: php
 */

namespace unionpay\MiniProgram\face;

use unionpay\Kernel\Client\MiniProgramClient;

/**
 * Class Client
 *
 * @package unionpay\MiniProgram\face
 */
class Client extends MiniProgramClient
{
    /**
     * 获取人脸识别照片
     * @param string $code 用户授权code
     * @param string $openId 用户唯一标识如果未传递code请确保已调用accessToken后再调用此接口
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Exception
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 19:23
     */
    public function image($code="", $openId = "")
    {
        $this->endpoint = "https://open.95516.com/open/access/1.0/face.image";

        $code = $code ?: $this->getCode($openId)['code'];
        if (!$code) throw new \Exception("code已失效，请重新授权获取");
        // 获取accessToken和openId
        $access = $this->app->access_token->getToken($code);

        $params = [
            'appId' => $this->config['appid'],
            'openId' => $openId ?: $access['openId'],
            'backendToken' => $this->app->backend_token->getToken()['backendToken'],
            'accessToken' => $access['accessToken']
        ];

        return $this->requestToken($params);
    }

    /**
     * 获取人脸识别视频
     * @param string $code 用户授权code
     * @param string $openId 用户唯一标识如果未传递code请确保已调用accessToken后再调用此接口
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Exception
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 19:23
     */
    public function video($code="", $openId = "")
    {
        $this->endpoint = "https://open.95516.com/open/access/1.0/face.video";

        $code = $code ?: $this->getCode($openId)['code'];
        if (!$code) throw new \Exception("code已失效，请重新授权获取");
        // 获取accessToken和openId
        $access = $this->app->access_token->getToken($code);

        $params = [
            'appId' => $this->config['appid'],
            'openId' => $openId ?: $access['openId'],
            'backendToken' => $this->app->backend_token->getToken()['backendToken'],
            'accessToken' => $access['accessToken']
        ];

        return $this->requestToken($params);
    }

    /**
     * 通过openid获取code
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
}
