<?php


namespace Server\Pool;


use Swoole\Database\RedisConfig;
use Swoole\Database\RedisPool;

class RedisDatabasePool extends RedisPool
{
    /** @var RedisConfig */
    protected $config;

    public function __construct( RedisConfig $config, int $size = self::DEFAULT_SIZE)
    {
        $this->config = $config;
        parent::__construct($config,$size);
    }

    public function getStatus(){
        return $this->pool->stats();
    }

}


