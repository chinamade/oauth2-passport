<?php
/**
 * Created by PhpStorm.
 * User: Baihuzi
 * Date: 2017/2/21
 * Time: 15:45
 */

namespace LianYun\Passport\Commons;

use LianYun\Passport\Passport;

class Log
{
    
    public function Log()
    {
        
    }
    
    /**
     *    记录业务相关的日志 统一格式
     */
    public static function save_run_log($info, $file_pre_name = "")
    {
        if (empty($file_pre_name)) {
            $file_name = date("Ymd") . ".log";
        }
        else {
            $file_name = $file_pre_name . "_" . date("Ymd") . ".log";
        }
        Log::write_log($info, $file_name);
    }
    
    /**
     *    记录系统出错的日志
     */
    public static function save_err_log($info)
    {
        $file_name = "error_" . date("Ymd") . ".log";
        Log::write_log($info, $file_name);
    }
    
    /**
     *    将日志信息写入到文本文件
     */
    private static function write_log($info, $file_name)
    {
        $app  = Passport::app();
        $info = str_replace("\n", "", $info);//日志中去掉换行
        $path = $app->getParameter('app.dir.log');
        if (file_exists($path) == false) {
            mkdir($path);
            chmod($path, 0777);
        }
        $fp = fopen("$path/$file_name", "a");
        if (defined('ZHUISUMA')) {
            $info = ZHUISUMA . '|' . $info;
        }
        $log = "[" . date("Y-m-d H:i:s") . "]|" . $info . "\r\n";
        fwrite($fp, $log);
        fclose($fp);
    }
    
    /**
     *  记录 BAS 系统需要的log
     *  时区处理： 统一使用 北京时区，不改变其他业务的时区
     *
     * @info: json格式
     */
    public static function save_bas_log($info, $file_pre_name, $log_type, $log_path = '/data/basLog')
    {
        
        $former_timezone = date_default_timezone_get();
        
        date_default_timezone_set('Asia/Shanghai');
        
        if (file_exists($log_path) == false) {
            mkdir($log_path);
            chmod($log_path, 0777);
        }
        $file_name = $file_pre_name . '_' . $log_type . '_' . date('Ymd') . '.log';
        
        $handle = fopen("$log_path/$file_name", "a");
        if (defined('ZHUISUMA')) {
            $info = ZHUISUMA . '|' . $info;
        }
        $log = $info . "\r\n";
        fwrite($handle, $log);
        fclose($handle);
        
        // timezone set : former timezone
        date_default_timezone_set($former_timezone);
    }
}
