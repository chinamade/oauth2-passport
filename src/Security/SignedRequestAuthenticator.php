<?php
/**
 * Created by PhpStorm.
 * User: Baihuzi
 * Date: 2019/3/21
 * Time: 15:17
 */

namespace LianYun\Passport\Security;

use Amopi\Mlib\Http\ServiceProviders\Security\AbstractSimplePreAuthenticator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class SignedRequestAuthenticator extends AbstractSimplePreAuthenticator
{
    const PASSPORT_CORE_TOKEN_KEY = 'ly-token';
    
    /**
     * Parse the given request, and extract the credential information from the request
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function getCredentialsFromRequest(Request $request)
    {
        if ($request->query->has(self::PASSPORT_CORE_TOKEN_KEY)) {
            $jwtString = $request->query->get(self::PASSPORT_CORE_TOKEN_KEY);
        }
        elseif ($request->headers->has(self::PASSPORT_CORE_TOKEN_KEY)) {
            $jwtString = $request->headers->get(self::PASSPORT_CORE_TOKEN_KEY);
        }
        elseif ($request->cookies->has(self::PASSPORT_CORE_TOKEN_KEY)) {
            $jwtString = $request->cookies->get(self::PASSPORT_CORE_TOKEN_KEY);
        }
        else {
            throw new BadCredentialsException("JWT Token string not found in request!");
        }
        $ip = $request->getClientIp();
    
        return [$ip, $jwtString];
    }
}