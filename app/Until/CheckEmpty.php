<?php

declare(strict_types=1);

namespace App\Until;

class CheckEmpty
{

    public static function check($params)
    {
        foreach ($params as $key => $val) {
            if(empty($val)){
                return $key;
            }
        }
        return true;
    }
}