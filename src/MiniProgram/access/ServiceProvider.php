<?php


namespace unionpay\MiniProgram\access;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ServiceProvider
 * @package unionpay\MiniProgram\access
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}.
     */
    public function register(Container $app)
    {
        !isset($app['access_token']) && $app['access_token'] = function ($app) {
            return new AccessToken($app);
        };

        !isset($app['backend_token']) && $app['backend_token'] = function ($app) {
            return new BackendToken($app);
        };

        !isset($app['front_token']) && $app['front_token'] = function ($app) {
            return new FrontToken($app);
        };
    }
}
