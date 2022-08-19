<?php

namespace Server\Pool;


class PDOCommand
{
    protected $PDO;
    protected $errorCode;
    protected $errorInfo;

    public function __construct($PDO)
    {
        $this->PDO = $PDO;
    }

    public function __call( $name, $arguments ){
        return $this->PDO->{$name}(...$arguments);
    }

    public function query( $sql , $where , $reconnect=false ){
        $ret = [];
        $stm = $this->PDO->prepare( $sql );
        $ste = $stm->execute($where);
        if( $ste === false ){
            $errorCode = $stm->errorCode();
            $errorInfo = $stm->errorInfo();
            $this->errorCode = $errorCode;
            $this->errorInfo = $errorInfo;
            if( !empty($errorInfo) && $errorInfo[1] == 2006 && $reconnect===false ){
                return false;
//                $this->reconnect();
//                return $this->get( $sql , $where , true );
            }
        }else{
            $this->errorInfo = null;
        }
        $list =  $stm->fetchAll();
        if( !empty($list) ){
            foreach ($list as $k=>$v){
                $tmp = [];
                foreach ($v as $k1=>$v1){
                    if( !is_numeric($k1) ){
                        $tmp[$k1] = $v1;
                    }
                }
                $list[$k] = $tmp;
            }
        }
        return $list;
    }
    public function insert( $sql , $reconnect=false ){
        $ret = [];
        try{
            $ste = $this->PDO->exec( $sql );
            $insertId = $this->PDO->lastInsertId();
            $ret['ste'] = $ste;
            $ret['insertId'] = $insertId;
        }catch ( \PDOException $e  ){
            $msg = $e->getMessage();
            $ret['e'] = $e;
            $ret['msg'] = $msg;
            $errorCode = $this->PDO->errorCode();
            $errorInfo = $this->PDO->errorInfo();
            $this->errorCode = $errorCode;
            $this->errorInfo = $errorInfo;
            return false;
        }
        return $insertId;
    }

    public function getError(){
        return $this->errorInfo;
    }


}
