<?php

declare(strict_types=1);
/**
 * This file is part of server.
 */
namespace Server;


use Server\Core\File;

class Config
{

    public static $configMap = [];

    /**
     * @param $key
     * @desc 读取配置
     * @return string|array|null
     *
     */
    public static function get($key)
    {
        if( empty($key) ){
            return null;
        }
        $keyArr = explode('.',$key);
        if( !isset(self::$configMap[$keyArr[0]]) ) {
            return null;
        }
        $config = self::$configMap[$keyArr[0]];
        for ( $i=1 ; $i<count($keyArr) ; $i++ ){
            if( !isset($config[$keyArr[$i]]) ) {
                return null;
            }
            $config = $config[$keyArr[$i]];
        }
        return $config;
    }

    public static function load()
    {
        $configPath = BASE_PATH . DIRECTORY_SEPARATOR . 'config';
        $files = File::tree( $configPath , "/.php$/" );
        if (!empty($files)) {
            foreach ($files as $dir => $fileList) {
                foreach ($fileList as $file) {
                    if ('default.php' == $file) {
                        continue;
                    }
                    list( $fileName ) = explode( '.' , $file );
                    $filePath = $dir . DIRECTORY_SEPARATOR . $file;
                    $thisFileConfig = [ $fileName => include $filePath ];
                    self::$configMap = $thisFileConfig + self::$configMap ;
                }
            }
        }
    }

}
