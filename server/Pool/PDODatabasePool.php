<?php


namespace Server\Pool;


use Swoole\Database\PDOConfig;
use Swoole\Database\PDOPool;

class PDODatabasePool extends PDOPool
{
    /** @var int */
    protected $size = 64;

    /** @var PDOConfig */
    protected $config;

    public function __construct(PDOConfig $config, int $size = self::DEFAULT_SIZE)
    {
        $this->config = $config;
        parent::__construct($config,$size);
    }

    public function getStatus(){
        return $this->pool->stats();
    }

}


