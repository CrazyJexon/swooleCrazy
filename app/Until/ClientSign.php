<?php

declare(strict_types=1);

namespace App\Until;

use Server\Config;

class ClientSign
{

    public static function make_sign( $data )
    {
        //加密key
        $key = Config::get('config.client_md5_sign_key');
        //去掉ticket参数
        if(isset($data['ticket'])) unset($data['ticket']);
        if(isset($data['PHPSESSID'])) unset($data['PHPSESSID']);
        //参数键名按字母表顺序排序
        ksort($data);
        //组装签名串 a=1&b=2.key
        $ticket_str = urldecode(http_build_query($data));
        $md5_str = $ticket_str . $key;
        return md5( $md5_str );
    }


}