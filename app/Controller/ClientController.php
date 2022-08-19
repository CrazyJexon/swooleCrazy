<?php
declare(strict_types=1);
/**
 */
namespace App\Controller;
use Server\Core\PDO;
use Server\Core\Redis;
use App\Until\CheckEmpty;
use App\Until\ClientSign;

use Server\Core\Tables;
use Server\Coroutine\Context;

class ClientController extends ApiController
{


    public function get_table(Context $context){
        $table_small = Tables::get("small");
        $table_small_status = $table_small->stats();
        $table_server = Tables::get("server");
        $table_big_status = $table_server->stats();
        return [
            'info' => 1,
            'msg'  => 'ok',
            'table_small_status'  => $table_small_status,
            'table_big_status'  => $table_big_status,
        ];
    }




}
