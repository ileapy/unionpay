<?php
/**
 * User: cfn <cfn@leapy.cn>
 * Datetime: 2021/8/16 19:10
 * Copyright: php
 */

namespace unionpay\MiniProgram\user;

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
        $app['mobile'] = function ($app) {
            return new Mobile($app);
        };

        $app['auth'] = function ($app) {
            return new Auth($app);
        };

        $app['card'] = function ($app) {
            return new Card($app);
        };

        $app['card_token'] = function ($app) {
            return new CardToken($app);
        };

        $app['user_status'] = function ($app) {
            return new UserStatus($app);
        };
    }
}
