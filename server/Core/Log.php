<?php
namespace Server\Core;

use Server\Config;

class Log
{
    private static $log = true;

    private static $level = [
        'debug'   => 1,
        'info'    => 2,
        'notice'  => 3,
        'warning' => 4,
        'error'   => 5,
        'none'    => 6,
    ];
    private static $ignore_file_name = [
        'api_server'
    ];

    private static $level_default = 2;

    private static $log_arr = [];

    public static function debug($file_name , $message)
    {
        self::log( 'debug' , $file_name , $message );
    }

    public static function info($file_name , $message)
    {
        self::log( 'info' , $file_name , $message );
    }

    public static function notice($file_name , $message)
    {
        self::log( 'notice' , $file_name , $message );
    }

    public static function warning($file_name , $message)
    {
        self::log( 'warning' , $file_name , $message );
    }

    public static function error( $file_name , $message )
    {
        self::log( 'error' , $file_name , $message );
    }


    protected static function log( $level , $file_name , $message )
    {
        if (self::$log) {
            if( self::checkLevel($level) === true){
                $file_name_arr = explode('/',$file_name);
                $file = $file_name_arr[count($file_name_arr)-1];
                unset($file_name_arr[count($file_name_arr)-1]);
                $log_dir = Config::get('logger.api.path').date('Ymd').'/'.implode('/',$file_name_arr);
                if(!is_dir($log_dir)){
                    File::make($log_dir, 0755);
                }
                $fileArr = explode('.',$file);
                if( in_array( $fileArr[0] , self::$ignore_file_name )  && isset($message['res_str']) ){
                    if( is_array($message['res_str']) || is_object($message['res_str']) ){
                        $message['res_str'] = strlen(json_encode($message['res_str']));
                    }else{
                        $message['res_str'] = strlen($message['res_str']);
                    }
                }
                $data = json_encode($message,JSON_UNESCAPED_SLASHES );
                $log_file = $log_dir.DIRECTORY_SEPARATOR.$fileArr[0].'_'.date('Ymd').'.log';
                @file_put_contents($log_file, $data."\r\n" , FILE_APPEND);
            }
        }
//        if (self::$log) {
//            if( self::checkLevel($level) === true){
//                $file_name_arr = explode('/',$file_name);
//                $file = $file_name_arr[count($file_name_arr)-1];
//                unset($file_name_arr[count($file_name_arr)-1]);
//                $log_dir = Config::get('logger.api.path').date('Ymd').'/'.implode('/',$file_name_arr);
//                if(!is_dir($log_dir)){
//                    File::make($log_dir, 0755);
//                }
//                $data = json_encode($message,JSON_UNESCAPED_SLASHES );
//                $log_file = $log_dir.DIRECTORY_SEPARATOR.$file.'_'.date('Ymd').'.log';
//                @file_put_contents($log_file, $data."\r\n" , FILE_APPEND);
//            }
//        }
    }

    public static function api_log($file_name , $message )
    {
        self::log( 'info' , $file_name , $message );
    }

    protected static function checkLevel($level){
        if( empty(self::$level[$level]) ){
            return false;
        }
        if( self::$level[$level] < self::$level_default ){
            return false;
        }
        return true;
    }

}