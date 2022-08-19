<?php

namespace Server\Core;

use Server\Pool\PDOCo;
use Server\Pool\PDOPool;
use Server\Server;
use Swoole\Coroutine;
use Swoole\Coroutine\Channel;

/**
 * query( string $type , ...$sid , string $sql, array $where = [],array $dbConfig=[]) $type=game时，第二参数为sid,$type=auto时，$dbConfig需要传
 */
class PDO
{
    public $connect;

    static $_instance;

    public static $type;
    public static $config;

    public static function getDriver()
    {
        if( !empty(self::$_instance) ){
            return self::$_instance;
        }
        self::$_instance = new static();
        self::$_instance->connect = new PDOCo(new PDOPool());
        return self::$_instance;
    }

    public static function __callStatic( $name, $arguments ){
        $pdo = self::getDriver();
        $connect = $pdo->connect;

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
        print_r($arguments);
        print_r("PHP Fatal error:".$ret[1]->getMessage()." in ".$ret[1]->getFile().":".$ret[1]->getLine()."\r\n".$ret[1]->getTraceAsString()."\r\n");
        return false;
    }



}
