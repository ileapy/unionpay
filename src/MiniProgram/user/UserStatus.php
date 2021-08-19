<?php
/**
 * User: cfn <cfn@leapy.cn>
 * Datetime: 2021/8/17 16:14
 * Copyright: php
 */

namespace unionpay\MiniProgram\user;

use unionpay\Kernel\Client\MiniProgramClient;
/**
 * Class UserStatus
 *
 * @package unionpay\MiniProgram\user
 */
class UserStatus extends MiniProgramClient
{
    /**
     * @var string
     */
    protected $endpoint = "https://open.95516.com/open/access/1.0/user.status";

    /**
     * @var string
     */
    protected $openId = "";

    /**
     * @var mixed|string
     */
    private $markTime = "";

    /**
     * @param string $openId 用户唯一标识，通过获取授权访问令牌获取
     * @param string $markTime 标记时间 (yyyy-MM-dd hh:mm:ss) ,如要上送此字段,必须按照规定格式
     * @param bool $decrypt 是否解密返回
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 19:23
     */
    public function getUserStatus($openId, $markTime ="", $decrypt = true)
    {
        $this->openId = $openId;
        $this->markTime = $markTime;

        $data = $this->requestToken($this->getCredentials());

        // 解密返回
        if ($decrypt)
            foreach ($data as $k => $v)
                if ($v)
                    $data[$k] = $this->app->crypto->decrypt($data[$k]);

        return $data;
    }

    /**
     * @return array
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/17 23:31
     */
    protected function getCredentials()
    {
        $config = [
            'appId' => $this->config['appid'],
            'openId' => $this->openId,
            'backendToken' => $this->app->backend_token->getToken()['backendToken'],
        ];

        if ($this->markTime) $config['markTime'] = $this->markTime;
        return $config;
    }
}
