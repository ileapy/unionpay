<?php
/**
 * User: cfn <cfn@leapy.cn>
 * Datetime: 2021/8/19 9:14
 * Copyright: php
 */

namespace unionpay\MiniProgram\contract;

use unionpay\Kernel\Client\MiniProgramClient;

/**
 * Class SignStatus
 *
 * @package unionpay\MiniProgram\contract
 */
class SignStatus extends MiniProgramClient
{
    /**
     * @var string
     */
    protected $endpoint = "https://open.95516.com/open/access/1.0/contract.info";

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
     * @param string $openId 用户唯一标识如果未传递code请确保已调用accessToken后再调用此接口
     * @param string $planId 协议模板id由云闪付录入模板并分配给接入方
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 19:23
     */
    public function status($openId = "", $planId = "")
    {
        $this->openId = $openId;
        $this->planId = $planId;

        return $this->requestToken($this->getCredentials());
    }

    /**
     * @return array
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 19:23
     */
    protected function getCredentials()
    {
        return [
            'appId' => $this->config['appid'],
            'openId' => $this->openId,
            'backendToken' => $this->app->backend_token->getToken()['backendToken'],
            'planId' => $this->planId
        ];
    }
}
