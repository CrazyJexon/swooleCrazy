<?php

declare(strict_types=1);

namespace App\Until;

class ObjectBaseHandle
{

    public static function objectToArray($object){
        if(empty($object)){
            return [];
        }
        return json_decode(json_encode($object), true);
    }

}