<?php
namespace Server\Coroutine;

use Swoole\Http\Request;
use Swoole\Http\Response;

class Context
{
    /**
     * @var Request
     */
    private $request;
    /**
     * @var Response
     */
    private $response;

    /**
     * @var array 一个array，可以存取想要的任何东西
     */
    private $map = [];

    public function __construct(\swoole_http_request $request, \swoole_http_response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param $key
     * @param $val
     */
    public function set($key, $val)
    {
        $this->map[$key] = $val;
    }

    /**
     * @param $key
     * @return mixed|null
     */
    public function get($key)
    {
        if (isset($this->map[$key])) {
            return $this->map[$key];
        }

        return null;
    }


    public function getRealIp(): string
    {
        $headers = $this->request->header;

        if(isset($headers['x-forwarded-for'][0]) && !empty($headers['x-forwarded-for'][0])) {
            return $headers['x-forwarded-for'][0];
        } elseif (isset($headers['x-real-ip'][0]) && !empty($headers['x-real-ip'][0])) {
            return $headers['x-real-ip'][0];
        }

        $serverParams = $this->request->server;
        return $serverParams['remote_addr'] ?? '';
    }

    public function input($key=null,$default=null){
        $list = array_merge( empty($this->request->get) ? [] : $this->request->get , empty($this->request->post) ? [] : $this->request->post );
        if( $key === null ){
            return $list;
        }else{
            return $list[$key] ?? $default;
        }

    }

}