<?php
/**
 * User: cfn <cfn@leapy.cn>
 * Datetime: 2021/8/16 20:19
 * Copyright: php
 */

namespace unionpay\MiniProgram\des;

use unionpay\Kernel\ServiceContainer;
use unionpay\Kernel\Support\TripleEncrypt;
use unionpay\Kernel\Traits\HasHttpRequests;
use unionpay\Kernel\Traits\InteractsWithCache;

/**
 * Class TripleEncrypt
 *
 * @package unionpay\Kernel\Support
 */
class Crypto
{
    use HasHttpRequests;
    use InteractsWithCache;

    /**
     * @var ServiceContainer
     */
    protected $app;

    /**
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

    /**
     * 解密
     * @param $data
     * @return string
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 20:30
     */
    public function decrypt($data)
    {
        return TripleEncrypt::encrypt3DES($data, $this->config['symmetricKey']);
    }

    /**
     * 加密
     * @param $data
     * @return false|string
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 20:30
     */
    public function encrypt($data)
    {
        return TripleEncrypt::decrypt3DES($data, $this->config['symmetricKey']);
    }
}
