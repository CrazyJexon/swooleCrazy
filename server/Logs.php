<?php

namespace Server;

use Server\Event\BeforeMainServerStart;
use Server\Event\OnManagerStart;
use Server\Event\OnRequest;
use Server\Event\OnStart;
use Server\Event\OnTask;
use Server\Event\OnWorkerStart;
use Swoole\Constant;
use Swoole\Http\Server as SwooleHttpServer;
use Swoole\Server as SwooleServer;
use Swoole\WebSocket\Server as SwooleWebSocketServer;

class Logs{

    public const SERVER_HTTP = 1;

    public const SERVER_WEBSOCKET = 2;

    public const SERVER_BASE = 3;

    /**
     * @var \Swoole\Http\Server
     */
    private static $server;


    public function start(){
        $before_service = BeforeMainServerStart::init();
        self::$server = $this->makeServer(
            Config::get('log.servers.type')  ,
            Config::get('log.servers.host') ,
            Config::get('log.servers.port') ,
            Config::get('log.mode') ,
            Config::get('log.servers.sock_type')  );
        self::$server->set($this->makeConfig());
        $this->bindBeforeService($before_service);
        $this->makeOnEvent();
        self::$server->start();
    }

    protected function makeServer(int $type, string $host, int $port, int $mode, int $sockType)
    {
        switch ($type) {
            case self::SERVER_HTTP:
                return new SwooleHttpServer($host, $port, $mode, $sockType);
            case self::SERVER_WEBSOCKET:
                return new SwooleWebSocketServer($host, $port, $mode, $sockType);
            case self::SERVER_BASE:
                return new SwooleServer($host, $port, $mode, $sockType);
        }
        throw new \Error('Server type is invalid.');
    }
    protected function makeConfig(){
        return Config::get('log.settings') + [
                'reactor_num'     => 1,     // reactor thread num
                'worker_num'      => 1,     // worker process num
                'task_worker_num' => 0,     // task worker process num
                'backlog'         => 128,   // listen backlog
                'max_request'     => 1,
                'dispatch_mode'   => 1,
                'max_wait_time'   => 60,
            ];
    }
    protected function makeOnEvent(){
        $eventConfig = [
            Constant::EVENT_START         => OnStart::class,
            Constant::EVENT_MANAGER_START => OnManagerStart::class,
            Constant::EVENT_WORKER_START  => OnWorkerStart::class,
            Constant::EVENT_TASK          => OnTask::class,
            Constant::EVENT_REQUEST       => OnRequest::class,
        ];
        foreach ( $eventConfig as $event => $handler ){
            self::$server->on( $event , [ $handler ,'init'] );
        }
    }

    /**
     * @param array $before_service
     * @return bool
     */
    protected function bindBeforeService( array $before_service ){
        foreach ($before_service as $k=>$v){
            $key = 'ext_'.$k;
            self::$server->$key = $v;
        }
        return true;
    }
    public static function getServer(){
        return self::$server;
    }

    public static function getExtServer($name){
        $key = 'ext_'.$name;
        return empty(self::$server->$key) ? null : self::$server->$key;
    }


}