<?php
/**
 * Created by PhpStorm.
 * User: qiudaoyu
 * Date: 2019/1/20
 * Time: 12:13 PM
 */

namespace LianYun\Passport\Controllers\ServerApi;

use LianYun\Passport\Commons\AppConfig;
use LianYun\Passport\Passport;
use LianYun\Passport\Commons\ChainedParameterObject;
use LianYun\Passport\Entities\Games;
use LianYun\Passport\Entities\Repositories\GamesRepository;
use LianYun\Passport\Entities\Repositories\UserRepository;
use LianYun\Passport\Entities\User;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\ValidationData;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class TokenController extends ServerApiBaseController
{
    public function __construct()
    {
    
    }
    
    public function meAction(Request $request)
    {
        $cp    = new ChainedParameterObject($request->request, $request->query);
        $appid = $cp->getMandatory('appid');
        $token = $cp->getMandatory('token');
        
        try {
            $user = $this->authenticateAndGetUser($appid, $token);
        } catch (\Exception $exception) {
            throw new BadRequestHttpException('Token: verification failed!', null, -1);
        }
        
        return [
            'status'=>'ok',
            'val'=>$user,
        ];
    }
    
    private function authenticateAndGetUser($appid, $jwt_access)
    {
        
        $appConfig = AppConfig::get($appid);
        if (!$appConfig) {
            throw new AccessDeniedHttpException("Invalid Client Id");
        }
        
        $parser         = new Parser();
        $validationData = new ValidationData();
        
        try {
            $token = $parser->parse($jwt_access);
            
            if (!$token->verify(new Sha256(), $appConfig['secret']) || !$token->validate($validationData)
            ) {
                throw new AccessDeniedHttpException(
                    "Invalid Logged In Token: verification failed!  Key:secret"
                );
            }
        } catch (\Exception $e) {
            throw new AccessDeniedHttpException($e->getMessage());
        }
        
        try {
            $jwt_uid      = $token->getClaim('uid');
            $jwt_appid    = $token->getClaim('appid');
            $jwt_checksum = $token->getClaim('checksum');
        } catch (\Exception $e) {
            throw new AccessDeniedHttpException(
                "Requested claim is not provided in JWT Token",
                null,
                ErrorCodeHandler::SDK_JWT_ERROR
            );
        }
        if ($jwt_appid != $appid) {
            throw new AccessDeniedHttpException(
                "Invalid Token: verification failed! Key:game_appid"
            );
        }
        
        $cksum = substr(
            md5(
                json_encode(
                    [
                        'iss'    => Passport::app()->getParameter('app.token_iss'),
                        'secret' => Passport::app()->getParameter('app.secret'),
                        'roles'  => [],
                    ]
                )
            ),
            16
        );
        
        if ($jwt_checksum != $cksum) {
            throw new AccessDeniedHttpException(
                "Invalid Token: verification failed! Key:cksum"
            );
        }
        
        $user = $this->getUser($jwt_uid);
        
        if (!$user) {
            throw new AccessDeniedHttpException(
                "Invalid Token: verification failed! Key:uid"
            );
        }
        
        return $user;
    }
    
    private function getGame($game_appid)
    {
        /** @var GamesRepository $gameRepo */
        $gameRepo = Passport::app()->getEntityManager()->getRepository(Games::class);
        $game     = $gameRepo->findByGameAppId($game_appid);
        
        return $game;
    }
    
    private function getUser($uid)
    {
        /** @var UserRepository $userRepo */
        $userRepo = Passport::app()->getEntityManager()->getRepository(User::class);
        /** @var User $user */
        $user = $userRepo->find($uid);
        
        return $user;
    }
}