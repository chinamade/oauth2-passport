<?php
/**
 * Created by PhpStorm.
 * User: Baihuzi
 * Date: 2019/3/21
 * Time: 11:21
 */

namespace LianYun\Passport\Controllers\OAuth;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\ValidationData;
use League\Uri\Components\Query;
use League\Uri\Http;
use LianYun\Passport\Commons\AppConfig;
use LianYun\Passport\Commons\CompanyHelper;
use LianYun\Passport\Entities\AuthorizationCode;
use LianYun\Passport\Entities\RefreshToken;
use LianYun\Passport\Entities\Repositories\UserRepository;
use LianYun\Passport\Entities\User;
use LianYun\Passport\Passport;
use LianYun\Passport\Security\OAuthAccessTokenGenerator;
use Amopi\Mlib\Http\ChainedParameterBagDataProvider;
use Amopi\Mlib\Http\SilexKernel;
use Amopi\Mlib\Utils\DataProviderInterface;
use LianYun\Passport\Security\SignedRequestAuthenticator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class OAuthController
{
    public function authorizeAction(Request $request,
                                    SilexKernel $kernel,
                                    EntityManager $entityManager,
                                    Passport $passport,
                                    OAuthAccessTokenGenerator $tokenGenerator)
    {
        
        $dp = new ChainedParameterBagDataProvider($request->query, $request->request);
        
        $loggedInToken = $dp->getOptional('logged_in_token');
        $clientId      = $dp->getMandatory('client_id');
        $state         = $dp->getOptional('state');
        /** @var User $user */
        $user      = null;
        $appConfig = AppConfig::get($clientId);
        if (!$appConfig) {
            throw new AccessDeniedHttpException("Invalid Client Id");
        }
        
        if ($loggedInToken) {
            try {
                $parser         = new Parser();
                $token          = $parser->parse($loggedInToken);
                $validationData = new ValidationData();
                if (!$token->validate($validationData)
                    || !$token->verify(new Sha256(), $kernel->getParameter('app.secret'))
                ) {
                    throw new AccessDeniedHttpException("Invalid Logged In Token: verification failed!");
                }
                if (!$token->hasClaim('uid')) {
                    throw new AccessDeniedHttpException("Unauthenticated Logged In Token!");
                }
            } catch (\Exception $e) {
                throw new AccessDeniedHttpException("Invalid Logged In Token");
            }
            
            $uid  = $token->getClaim('uid');
            $user = $entityManager->find(User::class, $uid);
            if (!$user) {
                throw new AccessDeniedHttpException("Unauthenticated Logged In Token: uid missing!");
            }
        }
        else {
            return new RedirectResponse(
                $kernel->path(
                    'oauth.login.check',
                    [
                        'redirect_uri' => $request->getUri(),
                    ]
                )
            );
        }
        
        $expectedRedirectUri = $appConfig['redirect_uri'];
        $redirectUri         = $dp->getOptional(
            'redirect_uri',
            DataProviderInterface::STRING_TYPE,
            $expectedRedirectUri
        );
        
        if (!$redirectUri) {
            throw new AccessDeniedHttpException("Redirect uri cannot be empty!");
        }
        if ($redirectUri != $expectedRedirectUri) {
            throw new AccessDeniedHttpException("Mismatching redirect URI");
        }
        
        $errors = ['state' => $state];
        
        try {
            $responseType = $dp->getMandatory('response_type');
            switch ($responseType) {
                case 'code':
                    {
                        $code = new AuthorizationCode();
                        $code->setUserId($user->getId());
                        $code->setExpirationTime(time() + 30);
                        $entityManager->persist($code);
                        $entityManager->flush();
                        
                        $uri = Http::createFromString($redirectUri);
                        $uri = $uri->withQuery(
                            (string)
                            Query::createFromParams(
                                [
                                    'state' => $state,
                                    'code'  => $code->getCode(),
                                ]
                            )
                        );
                        mdebug("Redirecting code authorization response to: %s", $uri);
                        
                        return new RedirectResponse((string)$uri);
                    }
                    break;
                default:
                    mwarning("Unsupported response type: %s", $responseType);
                    $errors['error'] = 'unsupported_response_type';
                    break;
            }
        } catch (\Exception $e) {
            mtrace($e, "Error occurce while processing OAuth/Authorize request", 'warning');
            $errors['error']             = 'server_error';
            $errors['error_description'] = sprintf("Error #%d, %s", $e->getCode(), $e->getMessage());
        }
        $uri = Http::createFromString($redirectUri);
        
        return new RedirectResponse((string)$uri);
    }
    
    public function tokenAction(Request $request,
                                EntityManager $entityManager,
                                Passport $passport,
                                OAuthAccessTokenGenerator $tokenGenerator,
                                CompanyHelper $companyHelper)
    {
        $dp                    = new ChainedParameterBagDataProvider($request->request);
        $tokenExpiresIn        = $passport->getParameter('app.token_lifetime');
        $refreshTokenExpiresIn = $passport->getParameter('app.refresh_token_lifetime');
        
        /** @var RefreshToken $refreshToken */
        $refreshToken = null;
        $authCode     = null;
        $user         = null;
        $grantType    = $dp->getMandatory('grant_type');
        
        switch ($grantType) {
            case 'password':
                {
                    $username  = $dp->getMandatory('username');
                    $password  = trim($dp->getMandatory('password'));
                    $validCode = $dp->getOptional('valid_code', DataProviderInterface::STRING_TYPE, '');
                    $appId     = $dp->getOptional('client_id', DataProviderInterface::INT_TYPE, 1);
                    
                    /** @var UserRepository $userRepo */
                    $userRepo = $entityManager->getRepository(User::class);
                    /** @var User $user */
                    $user = $userRepo->getUserForLoginCheck($username);
                    $ip   = $request->getClientIp();
                    if (!$user->verifyPassowrd($password)) {
                        throw new AccessDeniedHttpException(
                            "Username or password is invalid!"
                        );
                    }
                    $secret = $passport->getParameter('app.secret');
                    $token  = $tokenGenerator->create(
                        $user->getId(),
                        $appId,
                        [],
                        $secret,
                        $tokenExpiresIn,
                        $ip
                    );
                    
                }
                break;
            case 'register':
                {
                    $username = $dp->getMandatory('username');
                    $password = trim($dp->getMandatory('password'));
                    $ip       = $dp->getOptional('ip');
                    /** @var UserRepository $userRepo */
                    $userRepo = $entityManager->getRepository(User::class);
                    /** @var User $user */
                    $user  = $userRepo->createRecord($username, $password);
                    $appId = $dp->getOptional('client_id', DataProviderInterface::INT_TYPE, 0);
                    
                    $secret = $passport->getParameter('app.secret');
                    $token  = $tokenGenerator->create(
                        $user->getId(),
                        $appId,
                        [],
                        $secret,
                        $tokenExpiresIn,
                        $ip
                    );
                    
                }
                break;
            case 'authorization_code':
                {
                    $code  = $dp->getMandatory('code');
                    $appId = $dp->getMandatory('client_id');
                    
                    /** @var AuthorizationCode $authCode */
                    $authCode = $entityManager->getRepository(AuthorizationCode::class)->findOneBy(
                        [
                            'code' => $code,
                        ]
                    );
                    if (!$authCode) {
                        throw EntityNotFoundException::fromClassNameAndIdentifier(
                            AuthorizationCode::class,
                            ['code' => $code]
                        );
                    }
                    
                    $appConfig = AppConfig::get($appId);
                    if (!$appConfig) {
                        throw new AccessDeniedHttpException("Invalid Client Id");
                    }
                    
                    if ($authCode->getAppId() != $appId) {
                        throw new AccessDeniedHttpException(
                            "The authorization code is not for application: " . $appId
                        );
                    }
                    
                    if ($authCode->getExpirationTime() < time()) {
                        throw new AccessDeniedHttpException(
                            "The authorization code is already expired!"
                        );
                    }
                    
                    $roles = [];
                    
                    $token        = $tokenGenerator->create(
                        $authCode->getUserId(),
                        $appId,
                        $roles,
                        $appConfig['secret'],
                        $tokenExpiresIn
                    );
                    $refreshToken = new RefreshToken($refreshTokenExpiresIn);
                    $refreshToken->setUser($authCode->getUserId());
                    $refreshToken->setAppId($authCode->getAppId());
                    $entityManager->persist($refreshToken);
                    $entityManager->flush();
                }
                break;
            case 'client_credentials':
                {
                    $appId  = intval($request->getUser());
                    $secret = $request->getPassword();
                    
                    $appConfig = AppConfig::get($appId);
                    if (!$appConfig) {
                        throw new AccessDeniedHttpException("Invalid Client Id");
                    }
                    $roles = [];
                    $token = $tokenGenerator->create(
                        0,
                        $appId,
                        $roles,
                        $secret,
                        $tokenExpiresIn
                    );
                }
                break;
            case 'refresh_token':
                $secret = null;
                $code   = $dp->getMandatory('refresh_token');
                $appId  = intval($request->getUser());
                
                $appConfig = AppConfig::get($appId);
                if (!$appConfig) {
                    throw new AccessDeniedHttpException("Invalid Client Id");
                }
                
                /** @var RefreshToken $savedRefreshToken */
                $savedRefreshToken = $entityManager->getRepository(RefreshToken::class)->findOneBy(
                    [
                        'code' => $code,
                    ]
                );
                if (!$savedRefreshToken) {
                    throw EntityNotFoundException::fromClassNameAndIdentifier(
                        RefreshToken::class,
                        ['code' => $code]
                    );
                }
                if ($savedRefreshToken->getAppId() != $appId) {
                    throw new AccessDeniedHttpException("Refresh token is not issued for app: $appId");
                }
                $user = $savedRefreshToken->getUser();
                if (!$user) {
                    throw new AccessDeniedHttpException("Refresh token cannot be linked to a valid user!");
                }
                /** @var string[] $roles */
                $roles = [];
                if ($appConfig) {
                    $roles  = [];
                    $secret = $appConfig['secret'];
                }
                else {
                        $roles = [];
                    
                    $secret = $passport->getParameter('app.secret');
                }
                
                $token = $tokenGenerator->create(
                    $user,
                    $appId,
                    $roles,
                    $secret,
                    $tokenExpiresIn
                );
                break;
            case 'app_exchange':
                $ownSecret       = $request->getPassword();
                $withTokenString = $dp->getMandatory('with_token');
                $forAppId        = $dp->getMandatory('for_app', DataProviderInterface::INT_TYPE);
                $params          = $dp->getOptional('params', DataProviderInterface::ARRAY_TYPE, []);
                
                $appConfig = AppConfig::get($forAppId);
                if (!$appConfig) {
                    throw new AccessDeniedHttpException("Invalid Client Id");
                }
                
                $withToken      = (new Parser())->parse($withTokenString);
                $validationData = new ValidationData();
                if (!$withToken->validate($validationData)
                    || !$withToken->verify(new Sha256(), $ownSecret)
                ) {
                    throw new AccessDeniedHttpException("Invalid Logged In Token: verification failed!");
                }
                
                if ($withToken->getClaim('uid')) {
                    $token = $tokenGenerator->exchange(
                        $withToken,
                        $appConfig,
                        [],
                        $tokenExpiresIn,
                        $params
                    );
                }
                else {
                    $token = $tokenGenerator->exchange(
                        $withToken,
                        $appConfig,
                        [],
                        $tokenExpiresIn,
                        $params
                    );
                }
                break;
            case 'refresh_self_token':
                $secret = null;
                $code   = $dp->getMandatory('refresh_token');
                /** @var RefreshToken $savedRefreshToken */
                $savedRefreshToken = $entityManager->getRepository(RefreshToken::class)->findOneBy(
                    [
                        'code' => $code,
                    ]
                );
                if (!$savedRefreshToken) {
                    throw EntityNotFoundException::fromClassNameAndIdentifier(
                        RefreshToken::class,
                        ['code' => $code]
                    );
                }
                $jwtString = $request->headers->get(SignedRequestAuthenticator::PASSPORT_CORE_TOKEN_KEY);
                if (($companyHelper->validationToken($jwtString, $request->getClientIp()) !== true)
                    || $savedRefreshToken->getExpireAt() <= time()
                ) {
                    throw new AccessDeniedHttpException("Refresh token or self token already expired!");
                }
                $user = $savedRefreshToken->getUser();
                if (!$user) {
                    throw new AccessDeniedHttpException("Refresh token cannot be linked to a valid user!");
                }
                /** @var string[] $roles */
                $roles = [];
                
                if (!$companyHelper->checkIsCompany($passport, $request)) {
                    $tokenExpiresIn = $passport->getParameter('app.external_token_lifetime');
                }
                $secret = $passport->getParameter('app.secret');
                $token  = $tokenGenerator->create(
                    $user->getUserId(),
                    0,
                    $roles,
                    $secret,
                    $tokenExpiresIn,
                    $request->getClientIp()
                );
                break;
            default:
                throw new BadRequestHttpException("Unsupported grant type: " . $grantType);
                break;
        }
        
        $ret = [
            "access_token" => (string)$token,
            "token_type"   => "jwt",
            "expire_in"    => $tokenExpiresIn,
            'timestamp'    => time(),
        ];
        if ($refreshToken) {
            $ret['refresh_token']           = $refreshToken->getCode();
            $ret['refresh_token_expire_in'] = $refreshTokenExpiresIn;
        }
        
        //$ret["user_info"] = $user;
        
        return new JsonResponse($ret);
    }
    
    public function verifyAction(Request $request)
    {
    
    }
}


