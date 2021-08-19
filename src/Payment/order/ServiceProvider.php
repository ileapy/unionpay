<?php
/**
 * User: cfn <cfn@leapy.cn>
 * Datetime: 2021/8/16 19:10
 * Copyright: php
 */

namespace unionpay\Payment\order;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ServiceProvider
 *
 * @package unionpay\MiniProgram\user
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $app
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 19:49
     */
    public function register(Container $app)
    {
        $app['order'] = function ($app) {
            return new Client($app);
        };
    }
}
