<?php
/**
 * Created by PhpStorm.
 * User: Baihuzi
 * Date: 2019/3/21
 * Time: 14:35
 */

namespace LianYun\Passport\Security;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Token;
use LianYun\Passport\Passport;

class OAuthAccessTokenGenerator
{
    protected $coreSecret;
    
    public function __construct($coreSecret)
    {
        $this->coreSecret = $coreSecret;
    }
    
    public function create($userId, $appId, $roles, $secret, $expireIn = 3600, $ip = '')
    {
        $cksum   = substr(
            md5(
                json_encode(
                    [
                        'iss'    => Passport::app()->getParameter('app.token_iss'),
                        'secret' => $this->getCoreSecret(),
                        'roles'  => $roles,
                    ]
                )
            ),
            16
        );
        $now     = time();
        $builder = new Builder();
        $builder->setIssuer(Passport::app()->getParameter('app.token_iss'))
                ->setIssuedAt($now - 10)
                ->setExpiration($now + $expireIn)
                ->set('uid', $userId)
                ->set('appid', $appId)
                ->set('roles', $roles)
                ->set('ip', $ip)
                ->set('checksum', $cksum)
                ->sign(new Sha256(), $secret);
        $token = $builder->getToken();
        
        return $token;
    }
    
    public function exchange(Token $withToken, $appConfig, $roles, $expireIn, $params = [])
    {
        $cksum             = substr(
            md5(
                json_encode(
                    [
                        'iss'    => Passport::app()->getParameter('app.token_iss'),
                        'secret' => $this->getCoreSecret(),
                        'roles'  => $roles,
                    ]
                )
            ),
            16
        );
        $now               = time();
        $withTokenExpireAt = $withToken->getClaim('exp');
        if ($withTokenExpireAt < $now + $expireIn) {
            $expireIn = $withTokenExpireAt - $now;
            $expireIn = max(0, $expireIn);
        }
        
        $builder = new Builder();
        $builder->setIssuer(Passport::app()->getParameter('app.token_iss'))
                ->setIssuedAt($now)
                ->setExpiration($now + $expireIn)
                ->set('uid', $withToken->getClaim('uid'))
                ->set('appid', $appConfig['appid'])
                ->set('delegate', $withToken->getClaim('appid', 0))
                ->set('roles', $roles)
                ->set('ip', $withToken->getClaim('ip', ''))
                ->set('checksum', $cksum)
                ->set('params', $params)
                ->sign(new Sha256(), $appConfig['secret']);
        $token = $builder->getToken();
        
        return $token;
    }
    
    /**
     * @return mixed
     */
    public function getCoreSecret()
    {
        return $this->coreSecret;
    }
    
    /**
     * @param mixed $coreSecret
     */
    public function setCoreSecret($coreSecret)
    {
        $this->coreSecret = $coreSecret;
    }
}