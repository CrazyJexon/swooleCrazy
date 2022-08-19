<?php

namespace Server\Event;

use Server\Config;

class OnStart{

    public static function init( \Swoole\Server $server ){
        swoole_set_process_name("php ".Config::get('config.app_name')." master");
    }

}
