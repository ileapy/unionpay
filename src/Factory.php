<?php
/**
 * User: cfn <cfn@leapy.cn>
 * Datetime: 2021/8/15 22:51
 * Copyright: php
 */

namespace unionpay;

/**
 * Class Factory
 *
 * @method static \unionpay\MiniProgram\Application        miniProgram(array $config)
 * @method static \unionpay\Payment\Application            payment(array $config)
 * 
 * @package uniopay
 */
class Factory
{
    /**
     * @param $name
     * @param array $config
     * @return mixed
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/15 23:03
     */
    public static function make($name, array $config)
    {
        $name = ucfirst($name);
        $application = "\\unionpay\\{$name}\\Application";

        return new $application($config);
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/15 23:04
     */
    public static function __callStatic($name, $arguments)
    {
        return self::make($name, ...$arguments);
    }
}
