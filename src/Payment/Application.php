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
 * @package unionpay\Payment
 */
class Application extends ServiceContainer
{
    /**
     * @var array
     */
    protected $providers = [

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
