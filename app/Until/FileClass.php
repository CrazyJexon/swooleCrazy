<?php
//-------------------------------------------------------
// @Brief    : 文件系统相关操作类
// @Author   : chenjianxuan
// @Date     : 2022-7-14
//-------------------------------------------------------
namespace App\Until;
class FileClass
{

    /**
     * 遍历创建文件夹
     * @param $dir
     * @param int $mode
     * @return bool
     */
    public static function mk_dir($dir,$mode=0755){
        if( is_dir($dir) || @mkdir($dir,$mode) ){
            return true;
        }
        if( !self::mk_dir( dirname($dir) , $mode) ){
            return false;
        }
        return @mkdir( $dir , $mode );
    }

    /**
     * @param string $content
     * @param $log_file_name
     * @param $file_log
     */
    public static function make_log($content='', $log_file_name, $file_log=true ){
        if( $file_log ){
            $log_dir = storage_path('logs').DIRECTORY_SEPARATOR.'api_log/'.date('Ymd').'/';
        }else{
            $log_dir = storage_path('logs').DIRECTORY_SEPARATOR.'bbs_api_log/'.date('Ymd').'/';
        }
        if(!is_dir($log_dir)){
            self::mk_dir($log_dir, 0755);
        }
        $log_file = $log_dir.$log_file_name.'_'.date('Ymd').'.log';
//        $str = date('Y-m-d H:i:s').' content:'.$content."\r\n";
        @file_put_contents($log_file, $content."\r\n" , FILE_APPEND);
    }



    public function curlMakeRequest( $url, $params = array(), $expire = 0, $extend = array(),$order_header=array())
    {
        if (empty($url)) {
            return array('code' => '100');
        }
        $_curl = curl_init();
        $_header = array(
//            'Content-Type:application/json',
//            'charset:utf-8',
        );
        if(!empty($order_header)){
            foreach ($order_header as $ohk=>$ohv){
                $_header[] = $ohk.": ".$ohv;
            }
        }
        // 只要第二个参数传了值之后，就是POST的
        if (!empty($params)) {
            if( is_array($params) ){
                curl_setopt($_curl, CURLOPT_POSTFIELDS, http_build_query($params));
            }else{
                curl_setopt($_curl, CURLOPT_POSTFIELDS, $params );
            }
            curl_setopt($_curl, CURLOPT_POST, true);
        }

        if (substr($url, 0, 8) == 'https://') {
            curl_setopt($_curl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($_curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        }
        curl_setopt($_curl, CURLOPT_URL, $url);
        curl_setopt($_curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($_curl, CURLOPT_USERAGENT, 'API PHP CURL');
        curl_setopt($_curl, CURLOPT_HTTPHEADER, $_header);
        if ($expire > 0) {
            curl_setopt($_curl, CURLOPT_TIMEOUT, $expire); // 处理超时时间
            curl_setopt($_curl, CURLOPT_CONNECTTIMEOUT, $expire); // 建立连接超时时间
        }
        // 额外的配置
        if (!empty($extend)) {
            curl_setopt_array($_curl, $extend);
        }
        $result['result'] = curl_exec($_curl);
        $result['code'] = curl_getinfo($_curl, CURLINFO_HTTP_CODE);
        $result['info'] = curl_getinfo($_curl);
        if ($result['result'] === false) {
            $result['result'] = curl_error($_curl);
            $result['code'] = -curl_errno($_curl);
        }

        curl_close($_curl);
        return $result;
    }


}
