<?php
/**
 * User: cfn <cfn@leapy.cn>
 * Datetime: 2021/8/19 9:16
 * Copyright: php
 */

namespace unionpay\MiniProgram\contract;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ServiceProvider
 *
 * @package unionpay\MiniProgram\contract
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
        $app['apply'] = function ($app) {
            return new Apply($app);
        };

        $app['relieve'] = function ($app) {
            return new Relieve($app);
        };

        $app['sign_status'] = function ($app) {
            return new SignStatus($app);
        };

        $app['un_finished_order'] = function ($app) {
            return new UnFinishedOrder($app);
        };
    }
}
