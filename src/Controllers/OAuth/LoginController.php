<?php
/**
 * Created by PhpStorm.
 * User: Baihuzi
 * Date: 2019/3/21
 * Time: 15:05
 */

namespace LianYun\Passport\Controllers\OAuth;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use League\Uri\Components\Query;
use League\Uri\Http;
use LianYun\Passport\Commons\CompanyHelper;
use LianYun\Passport\Passport;
use LianYun\Passport\Security\SignedRequestAuthenticator;
use LianYun\Passport\Security\SignedRequestSender;
use LianYun\Passport\Entities\User;
use Amopi\Mlib\Http\ChainedParameterBagDataProvider;
use Amopi\Mlib\Http\SilexKernel;
use Amopi\Mlib\Utils\DataProviderInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;

class LoginController
{
    public function indexAction(Passport $passport, Request $request, SilexKernel $kernel, CompanyHelper $companyHelper)
    {
        /** @var SignedRequestSender $silexUser */
        $user      = null;
        $jwtString = null;
        $silexUser = $kernel->getUser();
        if ($silexUser instanceof SignedRequestSender) {
            $jwtString = $request->cookies->get(SignedRequestAuthenticator::PASSPORT_CORE_TOKEN_KEY);
            $user      = $silexUser->getUser();
        }
        
        $dp          = new ChainedParameterBagDataProvider($request->query);
        $redirectUri = $dp->getOptional(
            'redirect_uri',
            DataProviderInterface::STRING_TYPE,
            $kernel->path('home')
        );
        
        $client_id = 1;
        $state     = '';
        $paseUrl   = parse_url($redirectUri);
        if (isset($paseUrl['query'])) {
            parse_str($paseUrl['query'], $paramaters);
            $client_id = $paramaters['client_id'];
            $state     = $paramaters['state'] ?? '';
        }
        mdebug("Redirect uri for login/index: $redirectUri");
        $error_msg = '';
        
        if ($kernel->isGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)
            && $user
            && ($error_msg = $companyHelper->validationToken($jwtString, $request->getClientIp()) === true)
        ) {
            if (($redirectUri != '')
            ) {
                $uri                      = Http::createFromString($redirectUri);
                $authenticatedRedirectUri = (string)$uri;
                
                return new RedirectResponse($authenticatedRedirectUri);
            }
            else {
                return new RedirectResponse($kernel->path('home'));
            }
        }
        else {
            $isCompany = $companyHelper->checkIsCompany($passport, $request);
            
            return $kernel->render(
                'login.twig',
                [
                    'redirect_uri' => $redirectUri,
                    'is_company'   => $isCompany,
                    'error_code'   => $error_msg,
                    'client_id'    => $client_id,
                    'state'        => $state,
                ]
            );
        }
    }
    
    public function checkAction(SilexKernel $kernel, Request $request, Passport $passport)
    {

        $dp          = new ChainedParameterBagDataProvider($request->query);
        $redirectUri = $dp->getOptional('redirect_uri');
        
        if (!$redirectUri) {
            $redirectUri = $kernel->path('home');
        }
        /** @var SignedRequestSender $silexUser */
        $silexUser = $kernel->getUser();

        if (is_object($silexUser) && $silexUser->getUser() instanceof User) {
            /** @var SignedRequestSender $sender */
            $sender = $silexUser;
            $user   = $sender->getUser();
            $token = (new Builder())
                ->setExpiration(time() + 30)// max age of the temp token is 30s from now
                ->set('uid', ($user ? $user->getId() : 0))
                ->set('roles', [])
                ->sign(new Sha256(), $kernel->getParameter('app.secret'))
                ->getToken();
            
            $appendQueryString = "logged_in_token=" . (string)$token;
            $uri               = Http::createFromString($redirectUri);
            
            $uri                      = $uri->withQuery(
                (string)(new Query((string)$uri->getQuery()))->merge($appendQueryString)
            );
            $authenticatedRedirectUri = (string)$uri;
            
            return new RedirectResponse($authenticatedRedirectUri);
            
        }
        else {
            return new RedirectResponse(
                $kernel->path(
                    'oauth.login.index',
                    [
                        'redirect_uri' => $redirectUri,
                    ]
                )
            );
        }
    }
    
}