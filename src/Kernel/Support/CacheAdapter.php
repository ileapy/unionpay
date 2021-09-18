<?php
/**
 * User: cfn <cfn@leapy.cn>
 * Datetime: 2021/9/18 10:49
 * Copyright: php
 */

namespace unionpay\Kernel\Support;

use Symfony\Component\Cache\Adapter\ApcuAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\MemcachedAdapter;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use unionpay\Kernel\Config;

/**
 * 缓存适配器
 * Class CacheAdapter
 * @package unionpay\Kernel\Support
 */
class CacheAdapter
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var string
     */
    protected $type = 'file';

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config->toArray();
        $this->type = strtolower($this->config['cache']['type']);
    }

    /**
     * @return ApcuAdapter|FilesystemAdapter|MemcachedAdapter|RedisAdapter
     * @author cfn <cfn@leapy.cn>
     * @date 2021/9/18 11:42
     * @throws \ErrorException
     */
    public function getAdapter()
    {
        return $this->createAdapter();
    }

    /**
     * @throws \ErrorException
     * @author cfn <cfn@leapy.cn>
     * @date 2021/9/18 10:59
     */
    protected function createAdapter()
    {
        // 判断缓存方式是否支持
        if (!in_array($this->type,array('file','redis','apcu','memcached'))) throw new \Exception("不支持的缓存方式，仅支持：file, redis, apcu, memcached");
        switch ($this->type)
        {
            case 'redis':
                $redisClient = RedisAdapter::createConnection(
                    isset($this->config['redis']['dsn']) ? $this->config['redis']['dsn'] : 'redis://localhost:6379',
                    isset($this->config['redis']['redis_options']) ? $this->config['redis']['redis_options'] : []
                );
                return new RedisAdapter(
                    $redisClient,
                    isset($this->config['redis']['namespace']) ? $this->config['redis']['namespace'] : '',
                    isset($this->config['redis']['defaultLifetime']) ? $this->config['redis']['defaultLifetime'] : 0
                );
            case 'apcu':
                return new ApcuAdapter(
                    isset($this->config['apcu']['namespace']) ? $this->config['apcu']['namespace'] : '',
                    isset($this->config['apcu']['defaultLifetime']) ? $this->config['apcu']['defaultLifetime'] : 0,
                    isset($this->config['apcu']['version']) ? $this->config['apcu']['version'] : null);
            case 'memcached':
                $memcachedClient = MemcachedAdapter::createConnection(
                    isset($this->config['memcached']['dsn']) ? $this->config['memcached']['dsn'] : 'memcached://localhost:11211',
                    isset($this->config['memcached']['memcached_options']) ? $this->config['memcached']['memcached_options'] : []
                );
                return new MemcachedAdapter(
                    $memcachedClient,
                    isset($this->config['memcached']['namespace']) ? $this->config['memcached']['namespace'] : '',
                    isset($this->config['memcached']['defaultLifetime']) ? $this->config['memcached']['defaultLifetime'] : 0
                );
            default:
                return new FilesystemAdapter(
                    isset($this->config['file']['namespace']) ? $this->config['file']['namespace'] : '',
                    isset($this->config['file']['defaultLifetime']) ? $this->config['file']['defaultLifetime'] : 0,
                    isset($this->config['file']['directory']) ? $this->config['file']['directory'] : null);
        }
    }
}
