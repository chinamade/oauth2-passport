<?php
/**
 * Created by PhpStorm.
 * User: Baihuzi
 * Date: 2019/3/25
 * Time: 16:48
 */

namespace LianYun\Passport\Security;

use Amopi\Mlib\Http\ServiceProviders\Security\AbstractSimplePreAuthenticationPolicy;
use Symfony\Component\Security\Http\Authentication\SimplePreAuthenticatorInterface;
class OAuthTokenRequestPolicy extends AbstractSimplePreAuthenticationPolicy
{
    
    /**
     * @return SimplePreAuthenticatorInterface
     */
    protected function getPreAuthenticator()
    {
        return new OAuthTokenRequestAuthenticator();
    }
}