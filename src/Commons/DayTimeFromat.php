<?php
/**
 * Created by PhpStorm.
 * User: Baihuzi
 * Date: 2017/6/29
 * Time: 14:37
 */

namespace LianYun\Passport\Commons;

class DayTimeFromat
{
    static function get_yesterday_month($day)
    {
        return date("Ym", strtotime($day) - 3600 * 24);
    }
    
    static function get_start_time($day)
    {
        return strtotime(date("Y-m-d", strtotime($day) - 3600 * 24) . " 00:00:00");
    }
    
    static function get_end_time($day)
    {
        return strtotime(date("Y-m-d", strtotime($day) - 3600 * 24) . " 23:59:59");
    }
    
    static function get_last_date($day)
    {
        return date("Y-m-d", strtotime($day) - 3600 * 24);
    }
    
    static function get_start_time_bylast($day, $last = 7)
    {
        $last = $last + 1;
        
        return strtotime(date("Y-m-d", strtotime($day) - 3600 * 24 * $last) . " 00:00:00");
    }
    
    static function get_end_time_bylast($day, $last = 7)
    {
        $last = $last + 1;
        
        return strtotime(date("Y-m-d", strtotime($day) - 3600 * 24 * $last) . " 23:59:59");
    }
    
    static function get_month_start_time($day)
    {
        $d = date("d", strtotime($day));
        if ($d == "01") {
            return strtotime(date("Y-m", strtotime($day) - 3600 * 24) . "-01 00:00:00");
        }
        else {
            return strtotime(date("Y-m", strtotime($day)) . "-01 00:00:00");
        }
    }
    
    static function get_year_start_time()
    {
        return strtotime('20100101');
    }
    
    static function get_yesterday($day)
    {
        return date("Y-m-d", strtotime($day) - 3600 * 24);
    }
    
    static function get_last_month($day)
    {
        $firstday = date("Y-m-01", strtotime($day));
        $m        = date("Y-m", strtotime("$firstday -1 month"));
        
        return $m;
    }
    
    static function get_this_month($day)
    {
        $m = date("Y-m", strtotime($day));
        
        return $m;
    }
    
    static function get_next_month($day)
    {
        $firstday = date("Y-m-01", strtotime($day));
        $m        = date("Y-m", strtotime("$firstday +1 month"));
        
        return $m;
    }
}