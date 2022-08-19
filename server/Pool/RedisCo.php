<?php

namespace Server\Pool;

use Server\Config;

class RedisCo
{
    protected $pool;
    protected $config;

    public function __construct($pool)
    {
        $this->pool = $pool;
    }

    /**
     * @return mixed
     */
    public function connection($config,$timeout=60)
    {
        return $this->pool->get($config,$timeout);
    }


    public function __call($name, $arguments)
    {
        // TODO: Implement __call() method.
        if( in_array( strtolower($name) , RedisCommand::setTypeFunction() ) ){

            $configArr = Config::get('redis.config');
            if( empty($configArr) || !is_array($configArr) ){
                throw new \RuntimeException('Config empty');
            }
            $return = null;
            foreach ( $configArr as $redisName => $config ){
                $redisConnection = $this->connection($config);
                if( $redisConnection === false ){
                    $status = $this->pool->stats();
                    throw new \RuntimeException('redis '.$redisName.' get pool timeout 60s.'.json_encode($status)  );
                }
                $redis  = new RedisCommand($redisConnection);
                $return = $redis->$name(...$arguments);

                $this->pool->put($config,$redisConnection);
            }

            return $return;

        }else{


            $readConfig = Config::get('redis.config');
            $readRedisName = Config::get('redis.read');
            if( empty($readConfig[$readRedisName]) ){
                $config = $readConfig[array_key_first($readConfig)];
                $config['host'] = '127.0.0.1';
            }else{
                $config = $readConfig[$readRedisName];
            }


            $redisConnection = $this->connection($config);
            if( $redisConnection === false ){
                $status = $this->pool->stats($config);
                throw new \RuntimeException('redis '.$config['host'].' get pool timeout 60s.'.json_encode($status)  );
            }

            $redis  = new RedisCommand($redisConnection);
            $return = $redis->$name(...$arguments);

            $this->pool->put($config,$redisConnection);
            return $return;
        }

    }


    public function getPoolStatus(){
        $readConfig = Config::get('redis.config');
        $readRedisName = Config::get('redis.read');
        $config = empty($readConfig[$readRedisName]) ? [] : $readConfig[$readRedisName];
        $status = $this->pool->stats($config);
        return [$config,$status];
    }
}
