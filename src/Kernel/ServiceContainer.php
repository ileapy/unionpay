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
            ],
            'cache' => [
                // 详细参考 http://www.symfonychina.com/doc/current/components/cache/cache_pools.html
                // 可选值 File, Redis, APCu, memcached, Doctrine
                'type' => 'File',
            ],
            'file' => [
                // a string used as the subdirectory of the root cache directory, where cache
                // items will be stored
                'namespace' => '',

                // the default lifetime (in seconds) for cache items that do not define their
                // own lifetime, with a value 0 causing items to be stored indefinitely (i.e.
                // until the files are deleted)
                'defaultLifetime' => 0,

                // the main cache directory (the application needs read-write permissions on it)
                // if none is specified, a directory is created inside the system temporary directory
                'directory' => null
            ],
            'redis' => [
                // redis[s]://[pass@][ip|host|socket[:port]][/db-index]
                'dsn' => 'redis://localhost:6379',
                'redis_options' => [
                    'lazy' => false,
                    'persistent' => 0,
                    'persistent_id' => null,
                    'tcp_keepalive' => 0,
                    'timeout' => 30,
                    'read_timeout' => 0,
                    'retry_interval' => 0,
                ],
                // the default lifetime (in seconds) for cache items that do not define their
                // own lifetime, with a value 0 causing items to be stored indefinitely (i.e.
                // until RedisAdapter::clear() is invoked or the server(s) are purged)
                'defaultLifetime' => 0,

                // if ``true``, the values saved in the cache are serialized before storing them
                'storeSerialized' => true
            ],
            'apcu' => [
                // a string prefixed to the keys of the items stored in this cache
                'namespace' => '',

                // the default lifetime (in seconds) for cache items that do not define their
                // own lifetime, with a value 0 causing items to be stored indefinitely (i.e.
                // until the APCu memory is cleared)
                'defaultLifetime' => 0,

                // when set, all keys prefixed by $namespace can be invalidated by changing
                // this $version string
                'version' => null
            ],
            'memcached' => [
                // memcached://[user:pass@][ip|host|socket[:port]][?weight=int]
                'dsn' => 'memcached://localhost:11211',
                'memcached_type' => [
                    'compression' => true,
                    'libketama_compatible' => true,
                    'serializer' => 'igbinary',
                ],
                // a string prefixed to the keys of the items stored in this cache
                'namespace' => '',

                // the default lifetime (in seconds) for cache items that do not define their
                // own lifetime, with a value 0 causing items to be stored indefinitely (i.e.
                // until MemcachedAdapter::clear() is invoked or the server(s) are restarted)
                'defaultLifetime' => 0
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
