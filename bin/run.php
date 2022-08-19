#!/usr/bin/env php
<?php

ini_set('display_errors', 'on');
ini_set('display_startup_errors', 'on');
ini_set('memory_limit', '1G');

function debug_p( $str ){
    print_r($str);
    echo "\r\n";
}
error_reporting(E_ALL);
date_default_timezone_set( 'Asia/Shanghai' );

! defined('BASE_PATH') && define('BASE_PATH', dirname(__DIR__, 1));
! defined('SWOOLE_HOOK_FLAGS') && define('SWOOLE_HOOK_FLAGS', SWOOLE_HOOK_ALL);

require BASE_PATH . '/vendor/autoload.php';

(function () {
    (new \Server\Command())();
})();
