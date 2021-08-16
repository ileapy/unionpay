<?php

namespace unionpay\Test;

use unionpay\Factory;

class Test
{
    /**
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 10:58
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \HttpException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function index()
    {
        $config = [
            'appid' => 'a8c117dd0d644622a1b26034b63aff55',
            'secret' => '938e4310cb564af7b64544e4c86f2ed3',
            'symmetricKey' => 'f2dae558ea92a47a13d9166e8531e940f2dae558ea92a47a',
            'debug' => true,
            'merId' => '898150157321270',
            'pfx' => '',
            'pwd' => '235235',
            'cer' => ''
        ];
        $app = Factory::miniProgram($config);
        $frontToken = $app->front_token->getToken();
    }
}
