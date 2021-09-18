<?php
/**
 * User: cfn <cfn@leapy.cn>
 * Datetime: 2021/8/16 0:02
 * Copyright: php
 */

namespace unionpay\Kernel\Traits;

use unionpay\Kernel\ServiceContainer;
use unionpay\Kernel\Support\CacheAdapter;

/**
 * Class InteractsWithCache
 *
 * @package unionpay\Kernel\Traits
 */
trait InteractsWithCache
{
    /**
     * @var null
     */
    protected $cache = null;

    /**
     * @return CacheAdapter|void|null
     * @throws \Exception
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 9:46
     */
    protected function getCache()
    {
        if ($this->cache) {
            return $this->cache;
        }

        if (property_exists($this, 'app') && $this->app instanceof ServiceContainer && isset($this->app['cache'])) {
            $this->setCache($this->app['cache']);

            // Fix PHPStan error
            assert($this->cache instanceof CacheAdapter);

            return $this->cache;
        }

        return $this->cache = $this->createDefaultCache();
    }

    /**
     * @param $cache
     * @return $this
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 9:16
     */
    protected function setCache($cache)
    {
        $this->cache = $cache;

        return $this;
    }

    /**
     * @return \Symfony\Component\Cache\Adapter\ApcuAdapter|\Symfony\Component\Cache\Adapter\FilesystemAdapter|\Symfony\Component\Cache\Adapter\MemcachedAdapter|\Symfony\Component\Cache\Adapter\RedisAdapter|CacheAdapter
     * @throws \Exception
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 9:15
     */
    protected function createDefaultCache()
    {
        $cache = new CacheAdapter($this->app['config']);
        return $cache->getAdapter();
    }
}
