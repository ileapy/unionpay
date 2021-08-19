<?php
/**
 * User: cfn <cfn@leapy.cn>
 * Datetime: 2021/8/19 11:35
 * Copyright: php
 */

namespace unionpay\MiniProgram\config;

use unionpay\Kernel\Client\MiniProgramClient;
use unionpay\Kernel\Support\Str;

/**
 * Class Config
 *
 * @package unionpay\MiniProgram\config
 */
class Config extends MiniProgramClient
{
    /**
     * @var string
     */
    protected $url = "";

    /**
     * @param $url
     * @return array
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/19 11:53
     * @throws \Exception
     */
    public function getConfig($url)
    {
        $this->url = $url;

        if (!$this->url) throw new \Exception("请传递页面url地址，不包含#号之后的部分");

        return self::getCredentials();
    }

    /**
     * @return array
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/19 11:51
     */
    protected function getCredentials()
    {
        $nonceStr = Str::nonceStr();
        $timestamp = time();
        $signature = Str::signature([
            'appId'=>$this->config['appid'],
            'secret'=>$this->config['secret'],
            'timestamp'=>$timestamp,
            'url'=>$this->url,
            'nonceStr'=>$nonceStr,
            'frontToken'=>$this->app->front_token->getToken()['frontToken']
        ]);

        return [
            'appId' => $this->config['appid'],
            'timestamp' => $timestamp,
            'nonceStr' => $nonceStr,
            'signature' => $signature,
            'debug' => $this->config['debug']
        ];
    }
}
