<?php

namespace Server\Core;

use Server\Config;
use Server\Pool\RedisCo;
use Server\Pool\RedisPool;
use Swoole\Coroutine;
use Swoole\Coroutine\Channel;

class Redis
{
    public $connect;

    static $_instance;

    public static function getDriver(): Redis
    {
        if( !empty(self::$_instance) ){
            return self::$_instance;
        }
        self::$_instance = new static();
        self::$_instance->connect = new RedisCo(new RedisPool());
        return self::$_instance;
    }

    public static function __callStatic( $name, $arguments ){
        if(!empty($arguments[0])){
            $arguments[0] = self::handlePrefix($arguments[0]);
        }

        $redis = self::getDriver();
        $connect = $redis->connect;

        $chan = new Channel(1);
        Coroutine::create(function () use ($chan,$connect,$name,$arguments){
            try {
                $data = $connect->{$name}(...$arguments);
                $chan->push([true,$data]);
            } catch (\Exception $e) { //程序异常
                $chan->push([false,$e]);
            }
        });
        $ret = $chan->pop();
        if( $ret[0] === true ){
            return $ret[1];
        }
        print_r( date('Y-m-d H:i:s')."\r\n" );
        print_r("PHP Fatal error:".$ret[1]->getMessage()." in ".$ret[1]->getFile().":".$ret[1]->getLine()."\r\n".$ret[1]->getTraceAsString()."\r\n");
        return $ret;
    }

    public static function handlePrefix(string $key): string
    {
        $redis_prefix = Config::get('redis.redis_prefix');
        return $redis_prefix.$key;
    }

}
