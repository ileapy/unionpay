<?php
/**
 * User: cfn <cfn@leapy.cn>
 * Datetime: 2021/8/15 22:55
 * Copyright: php
 */

namespace unionpay\Payment;

use unionpay\Kernel\ServiceContainer;

/**
 * Class Application
 *
 * @property \unionpay\Payment\order\Client                    $order
 * @property \unionpay\Payment\notify\Client                   $notify
 * @property \unionpay\Payment\signature\Client                $signature
 * @property \unionpay\Payment\preorder\Client                 $preorder
 * @property \unionpay\Payment\file\Client                     $file
 *
 * @package unionpay\Payment
 */
class Application extends ServiceContainer
{
    /**
     * @var array
     */
    protected $providers = [
        order\ServiceProvider::class,
        notify\ServiceProvider::class,
        signature\ServiceProvider::class,
        preorder\ServiceProvider::class,
        file\ServiceProvider::class
    ];

    /**
     * @var string[]
     */
    protected $defaultConfig = [
        // 是否测试模式， 测试模式下网关为测试地址
        'payment_model' => false,
        // 请求数据类型
        'http_post_data_type' => 'form_params',
        // 正式环境域名
        'base_uri' => 'https://gateway.95516.com/gateway/api/',
        // 测试环境域名
        'test_base_uri' => 'https://gateway.test.95516.com/gateway/api/',
        // 正式文件传输地址
        'file_uri' => 'https://filedownload.95516.com/',
        // 测试文件传输地址
        'test_file_uri' => 'https://filedownload.test.95516.com/',
        // 生产环境基础证书
        'cert' => [
            'middleCertPath' => __DIR__ . DIRECTORY_SEPARATOR . 'cert' . DIRECTORY_SEPARATOR . 'acp_prod_middle.cer',
            'rootCertPath' => __DIR__ . DIRECTORY_SEPARATOR . 'cert' . DIRECTORY_SEPARATOR . 'acp_prod_root.cer',
        ],
        // 测试环境基础证书
        'test_cert' => [
            'middleCertPath' => __DIR__ . DIRECTORY_SEPARATOR . 'cert' . DIRECTORY_SEPARATOR . 'acp_test_middle.cer',
            'rootCertPath' => __DIR__ . DIRECTORY_SEPARATOR . 'cert' . DIRECTORY_SEPARATOR . 'acp_test_root.cer',
        ],
        'http' => [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded;charset=UTF-8'
            ]
        ],
        'payConfig' => [
            // 报文版本号，固定5.1.0，请勿改动
            'version'          =>          '5.1.0',
            // 	默认取值：UTF-8
            'encoding'         =>          'utf-8',
            // 后台返回商户结果时使用，如上送，则发送商户后台交易结果通知，不支持换行符等不可见字符，如需通过专线通知，需要在通知地址前面加上前缀：专线的首字母加竖线ZX|如果不需要发后台通知，可以固定上送http://www.specialUrl.com
            'backUrl'          =>          'http://www.specialUrl.com',
            // 默认为 156
            'currencyCode'     =>          '156',
            // 接入类型 0：商户直连接入 1：收单机构接入 2：平台商户接入
            'accessType'       =>          '0',
            // 签名方法 非对称签名： 01（表示采用RSA签名） HASH表示散列算法 11：支持散列方式验证SHA-256 12：支持散列方式验证SM3
            'signMethod'       =>          '01',
            // 渠道类型 07-PC，08-手机
            'channelType'      =>          '08'
        ]
    ];

    /**
     * Handle dynamic calls.
     * @param $method
     * @param $args
     * @return mixed
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/18 22:24
     */
    public function __call($method, $args)
    {
        return $this->base->$method(...$args);
    }
}
