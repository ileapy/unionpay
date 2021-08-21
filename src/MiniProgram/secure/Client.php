<?php
/**
 * User: cfn <cfn@leapy.cn>
 * Datetime: 2021/8/20 23:45
 * Copyright: php
 */

namespace unionpay\MiniProgram\secure;

use unionpay\Kernel\Client\MiniProgramClient;
use unionpay\Kernel\Support\Str;

/**
 * Class Client
 *
 * @package unionpay\MiniProgram\secure
 */
class Client extends MiniProgramClient
{
    /**
     * 获取验证支付密码流水号
     * @param string $code 用户授权code
     * @param string $openId 用户唯一标识如果未传递code请确保已调用accessToken后再调用此接口
     * @param array $params 参数
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Exception
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/21 0:48
     */
    public function verifyPwd($code = "", $openId = "", $params = [])
    {
        $this->endpoint = "https://open.95516.com/open/access/1.0/verifyPwdSeqId";

        $code = $code ?: $this->getCode($openId)['code'];
        if (!$code) throw new \Exception("code已失效，请重新授权获取");
        // 获取accessToken和openId
        $access = $this->app->access_token->getToken($code);

        $base = [
            'appId' => $this->config['appid'],
            'openId' => $openId ?: $access['openId'],
            'nonceStr' => Str::nonceStr(),
            'timestamp' => time(),
            'accessToken' => $access['accessToken'],
            'bizOrderId' => date('YmdHis').rand(1000,9999),
            'bizType' => '4p',
            'merchantId' => $this->config['appid']
        ];
        $params = array_replace_recursive($base, $params);

        if (!isset($params['notifyUrl']))
            throw new \Exception('通知地址[notifyUrl]未上报');

        $params['signature'] = $this->app->crypto->sign($params);

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
