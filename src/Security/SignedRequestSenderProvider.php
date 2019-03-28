<?php
/**
 * Created by PhpStorm.
 * User: Baihuzi
 * Date: 2019/3/21
 * Time: 15:15
 */

namespace LianYun\Passport\Security;

use Doctrine\ORM\EntityManager;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\ValidationData;
use LianYun\Passport\Passport;
use LianYun\Passport\Entities\User;
use Amopi\Mlib\Http\ServiceProviders\Security\AbstractSimplePreAuthenticateUserProvider;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\User\UserInterface;

class SignedRequestSenderProvider extends AbstractSimplePreAuthenticateUserProvider
{
    /**
     * @var string
     */
    private $coreSecret;
    /**
     * @var EntityManager
     */
    private $entityManager;
    
    public function __construct($coreSecret, EntityManager $entityManager)
    {
        parent::__construct(SignedRequestSender::class);
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
        list($ip, $jwtString) = $credentials;
        $parser = new Parser();

        try {
            $jwtToken = $parser->parse($jwtString);
        } catch (\Exception $e) {
            throw new BadCredentialsException("Malformed JWT Token: " . $e->getMessage());
        }

        try {
            $issuer     = $jwtToken->getClaim('iss');
            $appid      = $jwtToken->getClaim('appid',888);
            $userId     = $jwtToken->getClaim('uid');
            $loggedInIp = $jwtToken->getClaim('ip');
            $roles      = $jwtToken->getClaim('roles', []);
            $checksum   = $jwtToken->getClaim('checksum');
        } catch (\OutOfBoundsException $e) {
            throw new BadCredentialsException("Requested claim is not provided in JWT Token");
        }
        
        if ($loggedInIp !== '' && $ip != $loggedInIp) {
            mwarning(
                "IP mismatch for user %d, authenticated ip = %s, current ip = %s",
                $userId,
                $loggedInIp,
                $ip
            );
            setcookie(
                SignedRequestAuthenticator::PASSPORT_CORE_TOKEN_KEY,
                null,
                0,
                '/',
                Passport::app()->getParameter('app.token_iss')
            );
            setcookie(
                'ip_mismacth',
                1,
                time() + 60,
                '/',
                Passport::app()->getParameter('app.token_iss')
            );
            throw new BadCredentialsException("Token granted to a different logged in IP");
        }
        
        $expectedChecksum = substr(
            md5(
                json_encode(
                    [
                        'iss'    => $issuer,
                        'secret' => $this->coreSecret,
                        'roles'  => $roles,
                    ]
                )
            ),
            16
        );
        if ($checksum != $expectedChecksum) {
            throw new BadCredentialsException("Token is not issued by Core!");
        }
        
        $secret = $this->coreSecret;

        $signer = new Sha256();
        if (!$jwtToken->verify($signer, $secret)) {
            throw new BadCredentialsException("The signature for JWT Token is invalid!");
        }

        /** @var User $user */
        $user = null;
        if ($userId) {
            $user = $this->entityManager->find(User::class, $userId);
            if (!$user) {
                throw new BadCredentialsException("User doesn't exist, id = $userId");
            }
        }

        $validationData = new ValidationData();
        $validationData->setIssuer(Passport::app()->getParameter('app.token_iss'));
        if (!$jwtToken->validate($validationData)) {
            throw new BadCredentialsException("JWT Token failed validation.");
        }

        $sender = new SignedRequestSender($this->entityManager, $appid, $userId, $roles);

        return $sender;
    }
}