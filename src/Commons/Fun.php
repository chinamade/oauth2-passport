<?php
/**
 * Created by PhpStorm.
 * User: qiudaoyu
 * Date: 2019/1/12
 * Time: 9:57 PM
 */

namespace LianYun\Passport\Commons;

use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

class Fun
{
    public static function requestJsonContentToArray(Request $request)
    {
        $contentType = 'json';
        $array       = [];
        if ($contentType == $request->getContentType() && is_string($request->getContent())) {
            $array = json_decode($request->getContent(), true);
        }
        
        return $array;
    }
    
    public static function getJsonRequest(Request $request)
    {
        return new ParameterBag(self::requestJsonContentToArray($request));
    }
    
    public static function createUserId()
    {
        $auto_uid = "1" . (int)date('ymdHis') . rand(100, 999);
        
        return $auto_uid;
    }
    
    public static function generateOrderId()
    {
        $msec = self::getMicrotime();
        
        return $msec . rand(100, 999) . rand(1000, 9999);
    }
    
    public static function getMicrotime()
    {
        list($usec, $sec) = explode(" ", microtime());
        $msec = round((1 + $usec) * 1000);
        
        return (int)(date('ymdHis') * 1000 + $msec) - 1000;
    }
    
    public static function sqlParaValid_Num($para, $len)
    {
        if (!is_numeric($para) || strlen($para) > $len) {
            return false;
        }
        
        return true;
    }
    
    public static function getIp()
    {
        $cur_ip = $_SERVER["HTTP_X_FORWARDED_FOR"] ??  $_SERVER["REMOTE_ADDR"];
        
        return $cur_ip;
    }
    
    public static function getReqUrl()
    {
        $req_url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        
        return $req_url;
    }
}