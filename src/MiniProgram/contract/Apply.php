<?php
/**
 * User: cfn <cfn@leapy.cn>
 * Datetime: 2021/8/19 9:12
 * Copyright: php
 */

namespace unionpay\MiniProgram\contract;

use unionpay\Kernel\Client\MiniProgramClient;

/**
 * Class Apply
 *
 * @package unionpay\MiniProgram\contract
 */
class Apply extends MiniProgramClient
{
    /**
     * @var string
     */
    protected $endpoint = "https://open.95516.com/open/access/1.0/user.auth";

    /**
     * @var string
     */
    protected $code = "";

    /**
     * @var string
     */
    protected $openId = "";

    /**
     * 协议ID
     * @var string
     */
    protected $planId = "";

    /**
     * @var string
     */
    protected $contractCode = "";

    /**
     * @param string $code 用户授权或静默授权获取的code和openid必传其一
     * @param string $openId 用户唯一标识如果未传递code请确保已调用accessToken后再调用此接口
     * @param string $planId 协议模板id由云闪付录入模板并分配给接入方
     * @param string $contractCode 接入方侧的签约协议号，由接入方自行生成
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\Cache\InvalidArgumentException
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 19:23
     */
    public function apply($code = "", $openId = "", $planId = "", $contractCode = "")
    {
        $this->code = $code;
        $this->openId = $openId;
        $this->planId = $planId;
        $this->contractCode = $contractCode;

        return $this->requestToken($this->getCredentials());
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
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 19:23
     */
    protected function getCredentials()
    {
        $code = $this->code ?: $this->getCode($this->openId)['code'];
        if (!$code) throw new \Exception("code已失效，请重新授权获取");
        // 获取accessToken和openId
        $access = $this->app->access_token->getToken($code);

        return [
            'appId' => $this->config['appid'],
            'accessToken' => $access['accessToken'],
            'openId' => $this->openId ?: $access['openId'],
            'backendToken' => $this->app->backend_token->getToken()['backendToken'],
            'planId' => $this->planId,
            'contractCode' => $this->contractCode
        ];
    }
}
