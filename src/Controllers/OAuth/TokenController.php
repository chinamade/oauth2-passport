<?php
/**
 * Created by PhpStorm.
 * User: Baihuzi
 * Date: 2019/3/25
 * Time: 14:50
 */

namespace LianYun\Passport\Controllers\OAuth;

use Amopi\Mlib\Http\ChainedParameterBagDataProvider;
use Amopi\Mlib\Http\SilexKernel;
use LianYun\Passport\Commons\CompanyHelper;
use LianYun\Passport\Commons\Fun;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class TokenController
{
    
    public function authenticateAction(Request $request,
                                       SilexKernel $kernel)
    {
        $dp       = new ChainedParameterBagDataProvider($request->request);
        $username = $dp->getMandatory('username');
        $password = $dp->getMandatory('password');
        $type     = $dp->getMandatory('type');
        
        $subrequest = Request::create(
            $kernel->path('oauth.token'),
            'POST',
            [
                'grant_type' => $type == 'login' ? 'password' : 'register',
                'username'   => $username,
                'password'   => $password,
                'ip'         => Fun::getIp(),
            ],
            $request->cookies->all(),
            $request->files->all(),
            $request->server->all()
        );
        
        $subrequest->setTrustedProxies($request->getTrustedProxies());
        
        $response = $kernel->handle($subrequest, HttpKernelInterface::SUB_REQUEST);
        
        return $response;
    }
    
    /**
     * @param Request     $request
     * @param SilexKernel $kernel
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function refreshAction(Request $request, SilexKernel $kernel)
    {
        $dp = new ChainedParameterBagDataProvider($request->request);
        
        $code       = $dp->getMandatory('refresh_token');
        $grant_type = $dp->getMandatory('grant_type');
        
        $subrequest = Request::create(
            $kernel->path('oauth.token'),
            'POST',
            [
                'grant_type'    => $grant_type,
                'refresh_token' => $code,
            ],
            $request->cookies->all(),
            $request->files->all(),
            $request->server->all()
        );
        $subrequest->setTrustedProxies($request->getTrustedProxies());
        
        $response = $kernel->handle($subrequest, HttpKernelInterface::SUB_REQUEST);
        
        return $response;
    }
    
}