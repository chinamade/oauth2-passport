<?php
/**
 * Created by PhpStorm.
 * User: Baihuzi
 * Date: 2019/3/27
 * Time: 18:39
 */

namespace LianYun\Passport\Commons;

use LianYun\Passport\Passport;

class AppConfig
{
    public static function get($appid)
    {
        $appid      = $appid;
        $appConfigs = Passport::app()->getParameter('app_config');
        
        return $appConfigs[$appid] ?? [];
    }
    
}