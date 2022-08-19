<?php

declare(strict_types=1);
/**
 */
use Swoole\Constant;

return [
    'mode' => SWOOLE_PROCESS,
    'servers' => [
        'name' => 'http',
        'type' => \Server\Server::SERVER_HTTP,
        'host' => '0.0.0.0',
        'port' => 9502,
        'sock_type' => SWOOLE_SOCK_TCP,
    ],
    'settings' => [
        Constant::OPTION_DAEMONIZE           => true,
        Constant::OPTION_ENABLE_COROUTINE    => false,// 开启内置协程
        Constant::OPTION_WORKER_NUM          => 1,// 设置启动的 Worker 进程数
        Constant::OPTION_PID_FILE            => BASE_PATH . '/runtime/log.pid',
        Constant::OPTION_LOG_FILE            => BASE_PATH . '/runtime/log.log',
        Constant::OPTION_LOG_LEVEL           => SWOOLE_LOG_INFO,
        Constant::OPTION_OPEN_TCP_NODELAY    => true,
        Constant::OPTION_MAX_COROUTINE       => 100000,
        Constant::OPTION_OPEN_HTTP2_PROTOCOL => true,
        Constant::OPTION_MAX_REQUEST         => 10000000,
        Constant::OPTION_SOCKET_BUFFER_SIZE  => 2 * 1024 * 1024,
        Constant::OPTION_BUFFER_OUTPUT_SIZE  => 2 * 1024 * 1024,
        Constant::OPTION_MAX_WAIT_TIME       => 60,

        // Task Worker 数量，根据您的服务器配置而配置适当的数量
        Constant::OPTION_TASK_WORKER_NUM => 0,
        // 因为 `Task` 主要处理无法协程化的方法，所以这里推荐设为 `false`，避免协程下出现数据混淆的情况
//        Constant::OPTION_TASK_ENABLE_COROUTINE => true,

    ],
];
