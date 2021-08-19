<?php

namespace unionpay\Test;

use unionpay\Factory;

class Test
{
    /**
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 10:58
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function index()
    {
        $config = [
            'appid' => '**************************',
            'secret' => '**************************',
            'symmetricKey' => '**************************',
            'debug' => true,
            'merId' => '**************************',
            'pfx' => '**************************',
            'pwd' => '**************************',
            'cer' => '**************************'
        ];
        $app = Factory::miniProgram($config);
        $frontToken = $app->front_token->getToken();
    }
}
