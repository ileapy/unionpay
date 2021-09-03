<?php
/**
 * User: cfn <cfn@leapy.cn>
 * Datetime: 2021/8/19 23:43
 * Copyright: php
 */

namespace unionpay\MiniProgram\contract;

use unionpay\Kernel\Client\MiniProgramClient;

/**
 * Class Client
 *
 * @package unionpay\MiniProgram\contract
 */
class Client extends MiniProgramClient
{
    /**
     * 申请签约
     * @param string $code 用户授权或静默授权获取的code和openid必传其一
     * @param string $openId 用户唯一标识如果未传递code请确保已调用accessToken后再调用此接口
     * @param string $planId 协议模板id由云闪付录入模板并分配给接入方
     * @param string $contractCode 接入方侧的签约协议号，由接入方自行生成
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Exception
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 19:23
     */
    public function apply($code = "", $openId = "", $planId = "", $contractCode = "")
    {
        $this->endpoint = "1.0/contract.apply";

        $code = $code ?: $this->getCode($openId)['code'];
        if (!$code) throw new \Exception("code已失效，请重新授权获取");

        // 获取accessToken和openId
        $access = $this->app->access_token->getToken($code);

        $params = [
            'appId' => $this->config['appid'],
            'accessToken' => $access['accessToken'],
            'openId' => $openId ?: $access['openId'],
            'backendToken' => $this->app->backend_token->getToken()['backendToken'],
            'planId' => $planId,
            'contractCode' => $contractCode
        ];

        return $this->requestToken($params);
    }

    /**
     * 解约
     * @param string $openId 用户唯一标识如果未传递code请确保已调用accessToken后再调用此接口
     * @param string $contractId 申请签约完成后返回委托免密支付协议 id
     * @param string $planId 协议模板id由云闪付录入模板并分配给接入方
     * @param string $contractCode 接入方侧的签约协议号，由接入方自行生成
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 19:23
     */
    public function relieve($openId = "", $contractId = "", $planId = "", $contractCode = "")
    {
        $this->endpoint = "1.0/contract.relieve";

        $params = [
            'appId' => $this->config['appid'],
            'openId' => $openId,
            'backendToken' => $this->app->backend_token->getToken()['backendToken'],
            'contractId' => $contractId,
            'planId' => $planId,
            'contractCode' => $contractCode
        ];

        return $this->requestToken($params);
    }

    /**
     * 签约状态
     * @param string $openId 用户唯一标识如果未传递code请确保已调用accessToken后再调用此接口
     * @param string $planId 协议模板id由云闪付录入模板并分配给接入方
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 19:23
     */
    public function signStatus($openId = "", $planId = "")
    {
        $this->endpoint = "1.0/contract.info";

        $params = [
            'appId' => $this->config['appid'],
            'openId' => $openId,
            'backendToken' => $this->app->backend_token->getToken()['backendToken'],
            'planId' => $planId
        ];

        return $this->requestToken($params);
    }

    /**
     * 未完成订单
     * @param string $openId 用户唯一标识如果未传递code请确保已调用accessToken后再调用此接口
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 19:23
     */
    public function unFinishedOrder($openId = "")
    {
        $this->endpoint = "1.0/contract.status";

        $params = [
            'appId' => $this->config['appid'],
            'openId' => $openId,
            'backendToken' => $this->app->backend_token->getToken()['backendToken']
        ];

        return $this->requestToken($params);
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
}
