<?php
/**
 * User: cfn <cfn@leapy.cn>
 * Datetime: 2021/8/19 9:33
 * Copyright: php
 */


namespace unionpay\Kernel\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Class EventDispatcherServiceProvider.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */
class EventDispatcherServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $pimple
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 0:50
     */
    public function register(Container $pimple)
    {
        !isset($pimple['events']) && $pimple['events'] = function ($app) {
            $dispatcher = new EventDispatcher();

            foreach ($app->config->get('events.listen', []) as $event => $listeners) {
                foreach ($listeners as $listener) {
                    $dispatcher->addListener($event, $listener);
                }
            }

            return $dispatcher;
        };
    }
}
