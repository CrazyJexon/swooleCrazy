<?php

namespace Server\Event;

use Server\Config;

class OnWorkerStart{

    public static function init( \Swoole\Server $server , $worker_id ){
        opcache_reset();
        if( $server->taskworker ) {
            swoole_set_process_name("php ".Config::get('config.app_name')." task worker ".$worker_id);
        } else {
            swoole_set_process_name("php ".Config::get('config.app_name')." event worker ".$worker_id);
        }
        echo date('Y-m-d H:i:s')." ".$worker_id." WorkerStart\r\n";

        try {
            //加载配置，让此处加载的配置可热更新
            Config::load();
            //初始化PDO连接池
            //初始化redis连接池

        } catch (\Exception $e) {
            //初始化异常，关闭服务
            print_r($e);
            $server->shutdown();
        } catch (\Throwable $throwable) {
            //初始化异常，关闭服务
            print_r($throwable);
            $server->shutdown();
        }
    }

}