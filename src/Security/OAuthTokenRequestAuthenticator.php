<?php
/**
 * Created by PhpStorm.
 * User: Baihuzi
 * Date: 2019/3/25
 * Time: 16:50
 */

namespace LianYun\Passport\Security;

use Amopi\Mlib\Http\ChainedParameterBagDataProvider;
use Amopi\Mlib\Http\ServiceProviders\Security\AbstractSimplePreAuthenticator;
use Amopi\Mlib\Utils\DataProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class OAuthTokenRequestAuthenticator extends AbstractSimplePreAuthenticator
{
    
    /**
     * Parse the given request, and extract the credential information from the request
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function getCredentialsFromRequest(Request $request)
    {
        $user   = $request->getUser();
        $secret = $request->getPassword();
        if ($user === null || $secret === null) {
            throw new BadCredentialsException("WWW Basic Authentication required to obatain an oauth token");
        }
        $appid    = intval($user);
        $dp       = new ChainedParameterBagDataProvider($request->request);
        $clientId = $dp->getOptional('client_id', DataProviderInterface::INT_TYPE);
        if ($clientId !== null && $clientId != $appid) {
            throw new BadCredentialsException(
                "Authenticating using an application id different from what is supplied in request!"
            );
        }
        
        return [
            'appid'  => $appid,
            'secret' => $secret,
        ];
    }
}