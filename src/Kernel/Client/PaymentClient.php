<?php
/**
 * User: cfn <cfn@leapy.cn>
 * Datetime: 2021/8/19 9:33
 * Copyright: php
 */

namespace unionpay\Kernel\Client;

use unionpay\Kernel\ServiceContainer;
use unionpay\Kernel\Traits\HasHttpRequests;
use unionpay\Kernel\Traits\InteractsWithCache;

/**
 * Class PaymentClient
 *
 * @package unionpay\Kernel\Client
 */
class PaymentClient
{
    use HasHttpRequests;
    use InteractsWithCache;

    /**
     * @var ServiceContainer
     */
    protected $app;

    /**
     * @var string
     */
    protected $requestMethod = 'POST';

    /**
     * 配置
     * @var array
     */
    protected $config = [];

    /**
     * AccessToken constructor.
     *
     * @param ServiceContainer $app
     */
    public function __construct(ServiceContainer $app)
    {
        $this->app = $app;
        $this->config = $app['config']->toArray();
    }
}
