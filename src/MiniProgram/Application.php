<?php
/**
 * User: cfn <cfn@leapy.cn>
 * Datetime: 2021/8/15 22:55
 * Copyright: php
 */

namespace unionpay\MiniProgram;

use unionpay\Kernel\ServiceContainer;

/**
 * Class Application
 *
 * @property \unionpay\MiniProgram\access\AccessToken            $access_token
 * @property \unionpay\MiniProgram\access\BackendToken           $backend_token
 * @property \unionpay\MiniProgram\access\FrontToken             $front_token
 * @property \unionpay\MiniProgram\user\Client                   $user
 * @property \unionpay\MiniProgram\contract\Client               $contract
 * @property \unionpay\MiniProgram\crypto\Crypto                 $crypto
 * @property \unionpay\MiniProgram\base\Client                   $base
 * @property \unionpay\MiniProgram\message\Client                $message
 * @property \unionpay\MiniProgram\redpack\Client                $redpack
 * @property \unionpay\MiniProgram\qual\Client                   $qual
 * @property \unionpay\MiniProgram\notify\Client                 $notify
 * @property \unionpay\MiniProgram\secure\Client                 $secure
 * @property \unionpay\MiniProgram\face\Client                   $face
 * @property \unionpay\MiniProgram\coupon\Client                 $coupon
 *
 * @package uniopay\MiniProgram
 */
class Application extends ServiceContainer
{
    /**
     * @var array
     */
    protected $providers = [
        access\ServiceProvider::class,
        user\ServiceProvider::class,
        crypto\ServiceProvider::class,
        contract\ServiceProvider::class,
        base\ServiceProvider::class,
        coupon\ServiceProvider::class,
        face\ServiceProvider::class,
        message\ServiceProvider::class,
        notify\ServiceProvider::class,
        qual\ServiceProvider::class,
        redpack\ServiceProvider::class,
        secure\ServiceProvider::class
    ];

    /**
     * @var string[]
     */
    protected $defaultConfig = [
        'http_post_data_type' => 'json',
        'http' => [
            'timeout' => 30.0,
            'verify' => false,
            'base_uri' => 'https://open.95516.com/open/access/'
        ],
        // 银联公钥 -注意格式要保持一致
        'publicKey' => <<<EOF
-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA0QRJ81dxUdJNXoJwx81d
vExIWP9zGhVVdYWKgOajcQI/5F1Qt67ipEL+pSh30P9roPBv6LWHb42z/htmPUrK
XJ4f/WspXkbfBZsERe8XT8NZRnSdR3iZ9RqJKMzgjOetuoeFzTQ5QBalQKfQN9g5
8FEY0wrGH8DbrRzRImsnOVl0vvdIrqvTji+vD6GzZ8egSz9HZ0e9fQKG4dI1nuH1
45OfHY/fNe23oWINbXfFpVWiw+WgTTf8XzjVERD3qAT4i3cwB8RdhNlk3ysW0EJr
t2/WOJiI2NNK3xzXohqPYdUDRA4aWbRPtIma5EtBcnLFm76mXwkTlk9PJm7CJA3c
2QIDAQAB
-----END PUBLIC KEY-----
EOF
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
