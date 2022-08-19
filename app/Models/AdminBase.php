<?php

declare (strict_types=1);
namespace App\Models;

use Server\Core\PDO;

/**
 * @property string $sname 服名
 * @property string $description 描述
 * @property string $sdb 服库名
 */
class AdminBase
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'admin_base';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'value', 'desc' ];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];


    public static function getBase( $key , $field = '*' ){
        $sql = "select $field from admin_base where `name`=:name";
        $where = [':name'=>$key];
        return PDO::query( 'admin_db', $sql , $where );
    }

    public static function getBaseVal( $key ){
        $info = self::getBase( $key , '`value`' );
        return empty($info[0]) ? '' : $info[0]['value'];
    }



}
