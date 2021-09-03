<?php
/**
 * User: cfn <cfn@leapy.cn>
 * Datetime: 2021/8/20 23:44
 * Copyright: php
 */

namespace unionpay\MiniProgram\message;

use unionpay\Kernel\Client\MiniProgramClient;

/**
 * Class Client
 *
 * @package unionpay\MiniProgram\message
 */
class Client extends MiniProgramClient
{
    /**
     * 小程序模板消息
     * @param $params
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 19:23
     */
    public function send($params)
    {
        $this->endpoint = "1.0/applet.msg";

        $base = [
            'appId' => $this->config['appid'],
            'isPush' => '1',
            'backendToken' => $this->app->backend_token->getToken()['backendToken'],
            'uiType' => '1',
            'destType' => 'applet'
        ];
        $params = array_replace_recursive($base, $params);

        // 必填项验证
        if (!isset($params['openId']) || !isset($params['mpId']) || !isset($params['templateId']) || !isset($params['desc']) || !isset($params['destInfo']))
            throw new \Exception('用户唯一标识[openId]或小程序id[mpId]或通知模板id[templateId]或通知详情(200个字符以内)[desc]或类型：applet就是小程序id，rn就是rn的dest html就是url native就是native的dest[destInfo]未上报');

        return $this->requestToken($params);
    }
}
