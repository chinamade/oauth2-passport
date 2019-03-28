<?php
/**
 * Created by PhpStorm.
 * User: Baihuzi
 * Date: 2019/3/21
 * Time: 15:05
 */

namespace LianYun\Passport\Controllers\OAuth;

use Amopi\Mlib\Http\ChainedParameterBagDataProvider;
use Amopi\Mlib\Http\SilexKernel;
use Amopi\Mlib\Utils\DataProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class LogoutController
{
    
    public function indexAction(Request $request, SilexKernel $kernel)
    {
        
        $dp           = new ChainedParameterBagDataProvider($request->query, $request->request);
        $redirect_uri = $dp->getOptional('redirect_uri', DataProviderInterface::STRING_TYPE, '/panel/login');
        $username     = $dp->getOptional('username', DataProviderInterface::STRING_TYPE, '');
        
        minfo(
            "lougout username =%s, client_ip =%s, http_referer =%s",
            $username,
            $request->getClientIp(),
            $_SERVER['HTTP_REFERER']
        );
        
        return $kernel->render('logout.twig', ['redirect_uri' => $redirect_uri]);
    }
}