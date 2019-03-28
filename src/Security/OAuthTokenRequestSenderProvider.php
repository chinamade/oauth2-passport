<?php
/**
 * Created by PhpStorm.
 * User: Baihuzi
 * Date: 2019/3/25
 * Time: 16:53
 */

namespace LianYun\Passport\Security;

use Doctrine\ORM\EntityManager;
use Amopi\Mlib\Http\ServiceProviders\Security\AbstractSimplePreAuthenticateUserProvider;
use LianYun\Passport\Commons\AppConfig;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\User\UserInterface;

class OAuthTokenRequestSenderProvider extends AbstractSimplePreAuthenticateUserProvider
{
    /**
     * @var
     */
    protected $coreSecret;
    /**
     * @var EntityManager
     */
    protected $entityManager;
    
    public function __construct($coreSecret, EntityManager $entityManager)
    {
        parent::__construct(OAuthTokenRequestSender::class);
        $this->coreSecret    = $coreSecret;
        $this->entityManager = $entityManager;
    }
    
    /**
     * @param mixed $credentials the credentials extracted from request
     *
     * @return UserInterface
     *
     * @throws AuthenticationException throws authentication exception if authentication by credentials failed
     */
    public function authenticateAndGetUser($credentials)
    {
        $appid  = $credentials['appid'];
        $secret = $credentials['secret'];
        if ($appid) {
            
            $appConfig = AppConfig::get($appid);
            if (!$appConfig) {
                throw new BadCredentialsException("AppId doesn't exist, id = $appid");
            }
            
            $expectedSecret = $appConfig['secret'];
        }
        else {
            $expectedSecret = $this->coreSecret;
        }
        
        if ($expectedSecret != $secret) {
            throw new BadCredentialsException("Failed to authenticate application!");
        }
        $sender = new OAuthTokenRequestSender($appid);
        
        return $sender;
    }
}