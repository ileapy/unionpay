<?php
/**
 * User: cfn <cfn@leapy.cn>
 * Datetime: 2021/8/15 23:31
 * Copyright: php
 */

namespace unionpay\Kernel;

use Pimple\Container;
use unionpay\Kernel\Providers\ConfigServiceProvider;
use unionpay\Kernel\Providers\EventDispatcherServiceProvider;
use unionpay\Kernel\Providers\HttpClientServiceProvider;
use unionpay\Kernel\Providers\RequestServiceProvider;

/**
 * Class ServiceContainer
 *
 * @property \unionpay\Kernel\Config                          $config
 *
 * @package unionpay\Kernel
 */
class ServiceContainer extends Container
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var array
     */
    protected $providers = [];

    /**
     * @var array
     */
    protected $defaultConfig = [];

    /**
     * @var array
     */
    protected $userConfig = [];

    /**
     * Constructor.
     */
    public function __construct(array $config = [], array $prepends = [], $id = null)
    {
        $this->userConfig = $config;

        parent::__construct($prepends);

        $this->id = $id;

        $this->registerProviders($this->getProviders());

        $this->aggregate();
    }

    /**
     * Return all providers.
     *
     * @return array
     */
    public function getProviders()
    {
        return array_merge([
            ConfigServiceProvider::class,
            HttpClientServiceProvider::class,
            EventDispatcherServiceProvider::class,
            RequestServiceProvider::class
        ], $this->providers);
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id ?: $this->id = md5(json_encode($this->userConfig));
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        $baseConfig = [
            'http' => [
                'timeout' => 30.0,
                'base_uri' => 'https://open.95516.com/',
                'verify' => false // 不验证https证书
            ]
        ];
        return array_replace_recursive($baseConfig, $this->defaultConfig, $this->userConfig);
    }

    /**
     * @param array $providers
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 0:27
     */
    public function registerProviders(array $providers)
    {
        foreach ($providers as $provider) {
            parent::register(new $provider());
        }
    }

    /**
     * Aggregate.
     */
    protected function aggregate()
    {
        foreach ($this->getConfig() as $key => $value) {
            $this['config']->set($key, $value);
        }
    }

    /**
     * Magic get access.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function __get($id)
    {
        return $this->offsetGet($id);
    }

    /**
     * Magic set access.
     *
     * @param string $id
     * @param mixed  $value
     */
    public function __set($id, $value)
    {
        $this->offsetSet($id, $value);
    }
}
