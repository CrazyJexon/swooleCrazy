<?php

namespace Server\Event;


use Server\Core\Log;
use Server\Coroutine\Context;
use Server\Coroutine\Coroutine;
use Server\Router\Router;

class OnRequest{

    public static function init( \Swoole\Http\Request $request, \Swoole\Http\Response $response ){
        //初始化根协程ID
        Coroutine::setBaseId();
        //初始化上下文
        $context = new Context($request, $response);
        //存放容器pool
        \Server\Pool\Context::getInstance()->put($context);
        //协程退出，自动清空
        \Swoole\Coroutine\defer(function () {
            //清空当前pool的上下文，释放资源
            \Server\Pool\Context::getInstance()->release();
        });

        $mcTimeStart = microtime(true);
        try {
            $result = Router::dispatch( $request->server['path_info'] );
            $mcTimeRun = intval(round(microtime(true) - $mcTimeStart, 3) * 1000);
            $log_str = [
                'req_time'  => date('Y-m-d H:i:s'),
                'mc'        => $mcTimeRun,
                'remote_ip' => $context->getRealIp(),
                'i_name'    => $request->server['path_info'],
                'local_ip'  => '',
                'req_str'   => $context->input(),
                'res_str'   => $result
            ];
            if( is_array($result) || is_object($result) ){
                $result = json_encode($result);
            }
            if( $response->isWritable() ){
                $len = strlen($result);
                if( $len >= 1024*1024*2 ){
                    for ( $i=0;$i<$len;$i+=1024*1024*2 ){
                        $response->write(mb_substr($result,$i,1024*1024*2));
                    }
                }else{
                    $response->end($result);
                }
            }else{
                $response->setStatusCode(403);
            }
            Log::api_log( $request->server['path_info'] , $log_str  );

        } catch (\Exception $e) { //程序异常
            print_r( date('Y-m-d H:i:s').' Exception '.$request->server['path_info']."\r\n" );
            print_r("PHP Exception:".$e->getMessage()." in ".$e->getFile().":".$e->getLine()."\r\n".$e->getTraceAsString()."\r\n");
            $response->end($e->getMessage());
        } catch (\Error $e) { //程序错误，Fatal error
            print_r( date('Y-m-d H:i:s').' error '.$request->server['path_info']."\r\n" );
            print_r("PHP Fatal error:".$e->getMessage()." in ".$e->getFile().":".$e->getLine()."\r\n".$e->getTraceAsString()."\r\n");
            $response->status(500);
        } catch (\Throwable $e) {  //兜底
            print_r( date('Y-m-d H:i:s').' Throwable '.$request->server['path_info']."\r\n" );
            print_r("PHP Throwable:".$e->getMessage()." in ".$e->getFile().":".$e->getLine()."\r\n".$e->getTraceAsString()."\r\n");
            $response->status(500);
        }

    }

}
