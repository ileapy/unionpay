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
 * @property \unionpay\Payment\order\Order                    $order
 * @property \unionpay\Payment\notify\Notify                  $notify
 * @property \unionpay\Payment\query\Query                    $query
 * @property \unionpay\Payment\refund\Refund                  $refund
 * @property \unionpay\Payment\cancel\Cancel                  $cancel
 * @property \unionpay\Payment\file\File                      $file
 *
 * @package unionpay\Payment
 */
class Application extends ServiceContainer
{
    /**
     * @var array
     */
    protected $providers = [
        cancel\ServiceProvider::class,
        file\ServiceProvider::class,
        notify\ServiceProvider::class,
        order\ServiceProvider::class,
        query\ServiceProvider::class,
        refund\ServiceProvider::class
    ];

    /**
     * @var string[]
     */
    protected $defaultConfig = [
        'http_post_data_type' => 'form_params',
        'cert' => [
            'middleCertPath' => __DIR__ . DIRECTORY_SEPARATOR . 'cert' . DIRECTORY_SEPARATOR . 'middle.cer',
            'rootCertPath' => __DIR__ . DIRECTORY_SEPARATOR . 'cert' . DIRECTORY_SEPARATOR . 'root.cer'
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
