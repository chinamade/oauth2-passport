<?php
/**
 * Created by PhpStorm.
 * User: Baihuzi
 * Date: 2019/3/25
 * Time: 16:47
 */

namespace LianYun\Passport\Security;

use Amopi\Mlib\Http\ServiceProviders\Security\AbstractSimplePreAuthenticationPolicy;
use Symfony\Component\Security\Http\Authentication\SimplePreAuthenticatorInterface;

class SignedRequestPolicy extends AbstractSimplePreAuthenticationPolicy
{
    /**
     * @return SimplePreAuthenticatorInterface
     */
    public function getPreAuthenticator()
    {
        return new SignedRequestAuthenticator();
    }
}