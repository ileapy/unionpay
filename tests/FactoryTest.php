<?php
/**
 * User: cfn <cfn@leapy.cn>
 * Datetime: 2021/11/29
 * Copyright: php
 */

namespace unionpay\Tests;

use PHPUnit\Framework\TestCase;
use unionpay\Factory;

/**
 * Class FactoryTest
 * @package unionpay\Tests
 */
class FactoryTest extends TestCase
{
    public $options = [
        //************************************************** 小程序相关配置
        'privateKey' => '',
        'appid' => 'a8c117dd0d644622a1b26034b63aff55',
        'secret' => '938e4310cb564af7b64544e4c86f2ed3',
        'symmetricKey' => 'f2dae558ea92a47a13d9166e8531e940f2dae558ea92a47a',
        'debug' => true,
        //************************************************** 支付相关配置
        'merId' => '777290058194258', // 商户编号
        'signCertPath' => 'E:\study\php\acp_test_sign.pfx', // 签名证书路径pfx结尾
        'signCertPwd' => '000000', // 签名证书密码
        'encryptCertPath' => 'E:\study\php\acp_test_enc.cer', // 敏感信息加密证书路径 cer结尾
        'payment_model' => true, // 支付模式，true为测试环境，false为生产环境，默认false
    ];

    /**
     * @var \unionpay\MiniProgram\Application
     */
    public $app;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->app = Factory::miniProgram($this->options);

    }

    /**
     * @author cfn <cfn@leapy.cn>
     * @date 2021/11/29
     */
    public function testMobile()
    {
        $this->assertEquals("18438622618",$this->app->crypto->decrypt("Bth5XXdhUQIQLYXOcAreTQ=="));
    }

    /**
     * @author cfn <cfn@leapy.cn>
     * @date 2021/11/29
     */
    public function testBackendToken()
    {
        $this->assertArrayHasKey("","");
    }
}
