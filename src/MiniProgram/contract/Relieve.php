<?php
/**
 * User: cfn <cfn@leapy.cn>
 * Datetime: 2021/8/19 9:12
 * Copyright: php
 */

namespace unionpay\MiniProgram\contract;

use unionpay\Kernel\Client\MiniProgramClient;

/**
 * Class Relieve
 *
 * @package unionpay\MiniProgram\contract
 */
class Relieve extends MiniProgramClient
{
    /**
     * @var string
     */
    protected $endpoint = "https://open.95516.com/open/access/1.0/contract.relieve";

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
     * @var string
     */
    private $contractId = "";

    /**
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
        $this->openId = $openId;
        $this->contractId = $contractId;
        $this->planId = $planId;
        $this->contractCode = $contractCode;

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
            'contractId' => $this->contractId,
            'planId' => $this->planId,
            'contractCode' => $this->contractCode
        ];
    }
}
