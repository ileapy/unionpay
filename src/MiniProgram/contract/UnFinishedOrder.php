<?php
/**
 * User: cfn <cfn@leapy.cn>
 * Datetime: 2021/8/19 9:16
 * Copyright: php
 */

namespace unionpay\MiniProgram\contract;

use unionpay\Kernel\Client\MiniProgramClient;

/**
 * Class UnFinishedOrder
 *
 * @package unionpay\MiniProgram\contract
 */
class UnFinishedOrder extends MiniProgramClient
{
    /**
     * @var string
     */
    protected $endpoint = "https://open.95516.com/open/access/1.0/contract.status";

    /**
     * @var string
     */
    protected $openId = "";

    /**
     * @param string $openId 用户唯一标识如果未传递code请确保已调用accessToken后再调用此接口
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 19:23
     */
    public function query($openId = "")
    {
        $this->openId = $openId;

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
            'backendToken' => $this->app->backend_token->getToken()['backendToken']
        ];
    }
}
