<?php

namespace Server\Router;

use FastRoute\Dispatcher;
use Server\Config;
use Server\Pool\Context;
use function FastRoute\simpleDispatcher;
use \FastRoute\RouteCollector;

/**
 */
class Router{

    /**
     * @throws \Exception
     * @desc 自动路由
     */
    public static function dispatch( $path_info )
    {
        /**
         * @var $context \Server\Coroutine\Context
         */
        $context = Context::getInstance()->get();
        $request = $context->getRequest();
        $path = $path_info;
        if ('/favicon.ico' == $path) {
            return '';
        }
        $r = Config::get('routes');

        //没有路由配置或者配置不可执行，则走默认路由
        if (empty($r) || !is_callable($r)) {
            return self::normal($path, $context);

        }

        //引入fastrouter，进行路由检测
        $dispatcher = simpleDispatcher($r);
        $routeInfo = $dispatcher->dispatch($request->getMethod(), $path);

        //匹配到了
        if (Dispatcher::FOUND === $routeInfo[0]) {

            //匹配的是数组, 格式：['controllerName', 'MethodName']
            if (is_array($routeInfo[1])) {
                $controllerName = "\\" . $routeInfo[1][0];
                $controller = new $controllerName();
                $methodName = $routeInfo[1][1];
//                if( !empty($routeInfo[1][2]) ){
//                    foreach ( $routeInfo[1][2] as $mid=>$middleware ){
//                        $middlewareName = "\\" . $middleware;
//                        $middlewareController = new $middlewareName();
//                        if( isset($routeInfo[1][2][$mid+1]) ){
//                            $middlewareNextName = "\\" . $routeInfo[1][2][$mid+1];
//                            $middlewareNextController = new $middlewareNextName();
//                            $resultTmp = $middlewareController->handle( $context , $middlewareNextController->handle );
//                        }else{
//                            $resultTmp = $middlewareController->handle( $context , $controller->$methodName );
//                        }
//                    }
//                }else{
                    $result = $controller->$methodName($context);
//                }
            }

            elseif (is_string($routeInfo[1])) {
                //字符串, 格式：App\Controller\IndexController@index
                list($controllerName, $methodName) = explode('@', $routeInfo[1]);
                $controllerName = "\\" . $controllerName;
                $controller = new $controllerName();
                $result = $controller->$methodName($context);

            } elseif (is_callable($routeInfo[1])) {
                //回调函数，直接执行
                $result = $routeInfo[1](...$routeInfo[2]);

            } else {
                throw new \Exception('router error');
            }
            return $result;
        }

        //没找到路由，走默认的路由 http://xxx.com/{controllerName}/{MethodName}
        if (Dispatcher::NOT_FOUND === $routeInfo[0]) {
            return self::normal($path, $context);
        }

        //匹配到了，但不允许的http method
        if (Dispatcher::METHOD_NOT_ALLOWED === $routeInfo[0]) {
            throw new \Exception("METHOD_NOT_ALLOWED");
        }
        return '';
    }

    /**
     * @param $path
     * @param $context
     * @return mixed
     * @desc 没有匹配到路由，走默认的路由规则 http://xxx.com/{controllerName}/{MethodName}
     */
    public static function normal($path, $context)
    {
        //默认访问 App\Controller\IndexController@index
        $controllerName = 'App\Controller\IndexController';
        $methodName = 'Index';
        $controllerName = "\\{$controllerName}";
        $controller = new $controllerName();
        return $controller->$methodName($context);
    }

}