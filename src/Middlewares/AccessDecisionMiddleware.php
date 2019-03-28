<?php
/**
 * Created by PhpStorm.
 * User: Baihuzi
 * Date: 2017/8/19
 * Time: 11:13
 */

namespace LianYun\Passport\Middlewares;

use Amopi\Mlib\Http\Middlewares\MiddlewareInterface;
use Amopi\Mlib\Http\SilexKernel;
use Amopi\Mlib\Utils\StringUtils;
use LianYun\Passport\Passport;
use LianYun\Passport\Middlewares\AccessDecisions\PanelApiAccessDecision;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AccessDecisionMiddleware implements MiddlewareInterface
{
    /**
     * @return bool
     */
    public function onlyForMasterRequest()
    {
        return true;
    }
    
    public function after(Request $request, Response $response)
    {
    }
    
    public function before(Request $request, Application $application)
    {
        $route = $request->get('_route');
        switch (true) {
            default:
            
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
