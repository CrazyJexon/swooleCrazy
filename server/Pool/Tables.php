<?php

namespace Server\Pool;

use Server\Config;
use Swoole\Table;

class Tables{

    private $tables = [];

    public function init(){
        $tables = Config::get('server.tables');
        if( !empty($tables) && is_array($tables) ){
            foreach ($tables as $key=>$value ){

                $table = new Table( $value['size'] ,1 );
                foreach ($value['columns'] as $column_key=> $column_info ){
                    $table->column($column_info['name'], $column_info['type'] , $column_info['size']  );
                }
                $table->create();
                $this->add( $key , $table );

            }
        }
        return $this;
    }


    /**
     * Add a swoole table to existing tables.
     * @param string $name
     * @param \Swoole\Table $table
     *
     * @return Tables
     */
    public function add(string $name, Table $table)
    {
        $this->tables[$name] = $table;
        return $this;
    }

    /**
     * Get a swoole table by its name from existing tables.
     *
     * @param string $name
     *
     * @return \Swoole\Table $table
     */
    public function get(string $name)
    {
        return $this->tables[$name] ?? null;
    }

    /**
     * Get all existing swoole tables.
     *
     * @return array
     */
    public function getAll()
    {
        return $this->tables;
    }

    /**
     * Dynamically access table.
     *
     * @param  string $key
     *
     * @return table
     */
    public function __get($key)
    {
        return $this->get($key);
    }




}
