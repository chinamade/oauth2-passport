<?php
/**
 * Created by PhpStorm.
 * User: Baihuzi
 * Date: 2019/1/28
 * Time: 15:06
 */

namespace LianYun\Passport\Controllers\ServerApi;

use Symfony\Component\HttpFoundation\Response;

class CallbackController
{
    public function __construct()
    {
    }
    
    public function twitterCallback()
    {
        return new Response();
    }
}