<?php

namespace Server\Core;

use Server\Server;

class Tables
{

    public static function get( $name ){
        return Server::getExtServer("table")->get($name);
    }

}
