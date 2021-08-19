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
 * @property \unionpay\MiniProgram\user\Card                     $card
 * @property \unionpay\MiniProgram\user\CardToken                $cardToken
 * @property \unionpay\MiniProgram\user\Auth                     $auth
 * @property \unionpay\MiniProgram\user\UserStatus               $user_status
 * @property \unionpay\MiniProgram\des\Crypto                    $crypto
 * @property \unionpay\MiniProgram\contract\Apply                $apply
 * @property \unionpay\MiniProgram\contract\Relieve              $relieve
 * @property \unionpay\MiniProgram\contract\SignStatus           $signStatus
 * @property \unionpay\MiniProgram\contract\UnFinishedOrder      $unFinishedOrder
 * @property \unionpay\MiniProgram\config\Config                 $upsdk_config
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
        des\ServiceProvider::class,
        contract\ServiceProvider::class,
        config\ServiceProvider::class
    ];

    /**
     * @var string[]
     */
    protected $defaultConfig = [
        'http_post_data_type' => 'json',
    ];

    /**
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
