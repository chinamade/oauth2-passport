<?php
/**
 * Created by PhpStorm.
 * User: Baihuzi
 * Date: 2017/8/19
 * Time: 11:13
 */

namespace LianYun\Passport\Middlewares;

use Amopi\Mlib\Http\Middlewares\AbstractMiddleware;
use Amopi\Mlib\Http\SilexKernel;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SenderInjectorMiddleware extends AbstractMiddleware
{
    public function after(Request $request, Response $response)
    {
        // TODO: Implement after() method.
    }
    
    public function before(Request $request, Application $application)
    {
        /** @var SilexKernel $application */
        $sender = $application->getUser();
        
        if ($sender) {
            $request->attributes->set('sender', $sender);
        }
    }
    
    public function getAfterPriority()
    {
        return false;
    }
    
    public function getBeforePriority()
    {
        return SilexKernel::LATE_EVENT;
    }
}
