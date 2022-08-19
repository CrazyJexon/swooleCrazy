<?php

namespace Server\Event;

use Server\Config;

class OnTask{

    public static function init( \Swoole\Server $server , int $task_id, int $src_worker_id, $data ){
        print_r("run task:".$task_id."\r\n");
    }

}