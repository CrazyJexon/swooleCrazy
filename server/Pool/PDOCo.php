<?php

namespace Server\Pool;

use Server\Config;
use Server\Pool\PDOCommand;
use Swoole\Coroutine;
use Swoole\Coroutine\Channel;
use Swoole\Runtime;

class PDOCo
{
    /*
     */
    protected $pool;
    protected $config;

    /*
     */
    private $PDO;

    public function __construct($pool)
    {
        $this->pool = $pool;
    }

    /**
     * @return mixed
     */
    public function connection($config,$timeout=60)
    {
        return $this->pool->get($config,$timeout);
    }


    public function setConfig($config){
        $this->config = $config;
    }

    public function __call($name, $arguments)
    {
        // TODO: Implement __call() method.
        if( empty($arguments[0]) || empty($arguments[1]) ){
            return false;
        }

        $config = [];
        switch ($arguments[0]){
            case 'game':
                //sql
                if( empty($arguments[2]) ){
                    return false;
                }
                $sql = "select db_host as 'host',db_port as 'port',sdb as 'database',db_user as 'username',db_pwd as 'password' from adminserverlist where id=:id ";
                $where = [':id'=>$arguments[1]];
                $config = \Server\Core\PDO::query('admin', $sql,$where);
                if( empty($config) ){
                    return false;
                }
                unset($arguments[0]);
                unset($arguments[1]);
                break;
            case 'auto':
                if( empty($arguments[count($arguments)-1]) ){
                    return false;
                }
                $config = $arguments[count($arguments)-1];
                if( empty($config) ){
                    return false;
                }
                unset($arguments[0]);
                unset($arguments[count($arguments)-1]);
                break;
            default:
                $config = Config::get('databases.'.$arguments[0]);
                if( empty($config) ){
                    return false;
                }
                unset($arguments[0]);
                break;
        }

        $pdoConnection = $this->connection($config);
        if( $pdoConnection === false ){
            $status = $this->pool->stats($config);
            throw new \RuntimeException('PDO '.$config['host'].' get pool timeout 60s.'.json_encode($status));
        }

//        try {
            $POD  = new PDOCommand($pdoConnection);
            $return = $POD->$name(...$arguments);
            $this->pool->put($config,$pdoConnection);
//        }catch ( \Exception $e ){
//            $this->pool->put($config,$pdoConnection);
//            throw new \RuntimeException('PDO '.$config['host'].' pdo query error.config:'.json_encode($config)." arguments:".json_encode($arguments)." error:".json_encode($e) );
//        }

        return $return;
    }



    public function getPoolStatus(){
        return $this->pool->stats();
    }

}
