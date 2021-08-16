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
 * @property \unionpay\MiniProgram\user\Mobile                   $mobile
 * @property \unionpay\MiniProgram\des\Crypto                    $crypto
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
        des\ServiceProvider::class
    ];

    /**
     * Handle dynamic calls.
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        return $this->base->$method(...$args);
    }
}
