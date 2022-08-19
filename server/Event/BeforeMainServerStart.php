<?php

namespace Server\Event;

use Server\Core\PDO;
use Server\Core\Redis;
use Server\Pool\Tables;

class BeforeMainServerStart{

    public static function init(){
        $before_service = [];
        $Tables = new Tables();
        $before_service['table'] = $Tables->init();

//        $before_service['redis'] = Redis::getDriver();
//
//        $before_service['PDO'] = PDO::getDriver();
        return $before_service;
    }

}