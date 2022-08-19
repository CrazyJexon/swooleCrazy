<?php

declare(strict_types=1);

namespace App\Controller;

use App\Models\AdminBase;
use Server\Core\PDO;
use Server\Core\Redis;
use Server\Coroutine\Context;

class UserController extends ApiController
{
    public function index1(Context $context){
        $cache_key = 'sh_server_config';
        $info = AdminBase::getBaseVal( $cache_key );
        return $info;
    }
    public function index2(Context $context){
        $name = $context->input('name');

        $sql = "select * from admin_base where `name`=:name";
        $where = [':name'=>$name];

        $config = [
            'host' => '192.168.6.79',
            'port' => '3306',
            'database' => 'dev',
            'username' => 'root',
            'password' => '12345678',
        ];
        $list = PDO::query( 'auto', $sql , $where , $config );
//        $list = PDO::query( 'admin_db', $sql , $where );
        return [$list];
    }

    public function get_redis( Context $context ){
        $key = $context->input('key');
        $list = Redis::get($key);
        return [$list];
    }


    public function hget_redis(Context $context){
        $key = $context->input('key');
        $column = $context->input('column');
        $list1 = Redis::hGet($key , $column );
        $list2 = Redis::hGetAll($key);
        return [$list1,$list2];
    }

    public function hMset_redis(Context $context){
        $key = $context->input('key');
        $column = $context->input('column');
        return Redis::hMset( 'aaaaaaaaaaa' , [$key=>$column] );
    }

    public function hgetall_redis(Context $context){
        $key = $context->input('key');
        $list = Redis::hGetAll($key);
        return [$list];
    }


    public function aaaaa(Context $context){
        return mt_rand(0,9999);
    }



    public function insert(Context $context){
        $sql = "insert into admin_base (`name`,`value`,`desc`) values ('aaaaaaaa','222222','333333')";
        $info = PDO::insert($sql);
        return [$info];
    }


}
