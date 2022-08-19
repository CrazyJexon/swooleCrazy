<?php


namespace Server\Pool;

use Swoole\Database\PDOConfig;

class PDOPool
{

    /**
     * @var
     */
    protected $config;

    /**
     * @var
     */
    protected $pool;

    public function  __construct()
    {
    }

    public function init($dbConfig=[])
    {
        $config = (new PDOConfig())
            ->withHost( $dbConfig['host'] )
            ->withPort( $dbConfig['port'] )
            ->withDbName( $dbConfig['database'] )
            ->withCharset( empty($dbConfig['charset']) ? 'utf8mb4' : $dbConfig['charset'] )
            ->withUsername( $dbConfig['username'] )
            ->withPassword( $dbConfig['password'] );

        $size = empty($dbConfig['size']) ? 64 : $dbConfig['size'];

        $this->pool[$dbConfig['database']] = [
            'last_time' => time(),
            'pool' => new PDODatabasePool($config,$size)
        ];
    }

    public function get($config , float $timeout=1)
    {
        if( empty($config['host']) || empty($config['port']) || empty($config['database']) || empty($config['username']) || empty($config['password']) ){
            throw new \RuntimeException('Config empty');
        }
        if( empty($this->pool[$config['database']]) ){
            $this->init($config);
        }
        $this->pool[$config['database']]['last_time'] = time();
        return $this->pool[$config['database']]['pool']->get($timeout);
    }

    public function put($config,$PDO)
    {
        return $this->pool[$config['database']]['pool']->put($PDO);
    }

    public function stats(){
        $list = [];
        foreach ($this->pool as $databases => $val ){
            $list[$databases] = $this->pool[$databases]['pool']->getStatus();
        }
        return $list;
    }



}


