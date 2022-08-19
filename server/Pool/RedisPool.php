<?php


namespace Server\Pool;

use Swoole\Database\RedisConfig;

class RedisPool
{
    protected $config;

    /**
     * @var
     */
    protected $pool;

    public function  __construct()
    {
    }

    public function init($dbConfig=[])
    {
        $config = (new RedisConfig())
            ->withHost($dbConfig['host'])
            ->withPort($dbConfig['port'])
            ->withAuth($dbConfig['auth'])
            ->withTimeout( empty($dbConfig['pool']['timeout']) ? 2.0 : floatval($dbConfig['pool']['timeout']) );

        $size = empty($dbConfig['pool']['size']) ? 64 : $dbConfig['pool']['size'];

        $this->pool[$dbConfig['host']] = [
            'last_time' => time(),
            'pool' => new RedisDatabasePool($config,$size)
        ];
        return $this->pool[$dbConfig['host']];
    }

    public function get($config,float $timeout=1)
    {
        $this->config = $config;
        if( empty($config['host']) || empty($config['port']) || empty($config['auth']) ){
            throw new \RuntimeException('Config empty');
        }
        if( empty($this->pool[$config['host']]) ){
            $this->init($config);
        }
        $this->pool[$config['host']]['last_time'] = time();
        return $this->pool[$config['host']]['pool']->get($timeout);
    }

    public function put($config,$redis)
    {
        return $this->pool[$config['host']]['pool']->put($redis);
    }

    public function stats($config){
        return $this->pool[$config['host']]['pool']->getStatus();
    }

}


