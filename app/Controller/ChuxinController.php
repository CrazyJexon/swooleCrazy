<?php

declare(strict_types=1);
/**
 */
namespace App\Controller;

use Server\Core\Redis;
use Server\Coroutine\Context;
use Server\Coroutine\Coroutine;

class ChuxinController extends ApiController
{

    /**
     * 所有的大区和服务器名
     * @var string
     */
    protected $all_server_list_cache_key = 'all_zone_server_name';

    public function set_task(Context $context){
        $container = ApplicationContext::getContainer();
        $exec = $container->get(TaskExecutor::class);
        $result = $exec->execute(new Task([ChuxinController::class, 'handle'], [Coroutine::getId()]));

        return [
            'info' => 1 ,
            'msg'=>'ok'.swoole_cpu_num()."____".\Server\Coroutine\Coroutine::getId(),
            'data'=>$result
        ];
    }

    public function set_all_server_list(Context $context)
    {
        $result = Redis::get( $this->all_server_list_cache_key );
        global $all_zone_server_name;
        $all_zone_server_name = $result;

        return [
            'info' => 1 ,
            'msg'=>'ok'.swoole_cpu_num()."____".\Server\Coroutine\Coroutine::getId()
        ];
    }

    public function get_all_server_list(Context $context)
    {
        $params['time'] = $context->input('time', 0);
        $sign = $context->input('sign', '');
        foreach ($params as $k=>$v){
            if( empty($v) ){
                return [
                    'info' => -1 ,
                    'msg'=>$k.'参数不能为空' ,
                    'data'=>[],
                ];
            }
        }
        unset($params['sign']);
        $key = env('SDK_MD5_SIGN_KEY');
        if(md5(urldecode(http_build_query($params)).$key) != $sign){
            return [
                'info' => -7 ,
                'msg'=>'签名验证失败' ,
                'data'=>[]
            ];
        }

        global $all_zone_server_name;
        if( empty($all_zone_server_name) ){
            $result = Redis::get( $this->all_server_list_cache_key );
            $all_zone_server_name = $result;
        }
        return [
            'info' => 1 ,
            'msg'=>'ok' ,
            'data'=>$all_zone_server_name
        ];
    }





}
