<?php
/**
 * Created by PhpStorm.
 * User: Baihuzi
 * Date: 2019/3/26
 * Time: 18:13
 */

namespace LianYun\Passport\Controllers\OAuth;

use Amopi\Mlib\Http\ChainedParameterBagDataProvider;
use Amopi\Mlib\Http\ServiceProviders\Cookie\ResponseCookieContainer;
use Amopi\Mlib\Http\SilexKernel;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Request;

class TestController
{
    public function testAction(Request $request,SilexKernel $kernel, ResponseCookieContainer $cookieContainer){
        $dp             = new ChainedParameterBagDataProvider($request->query);
        $code           = $dp->getMandatory('code');
        $state          = $dp->getOptional('state');
        $appId          = 888;
        $appSecret      = 'abc3df';
        $tokenKeyPrefix = 'test_';
    
        $uri      = 'http://oauth-test.passport.com/oauth/token';
        $client   = new Client();
        $response = $client->request(
            "POST",
            $uri,
            [
                'auth'        => [$appId, $appSecret],
                'form_params' => [
                    'grant_type' => 'authorization_code',
                    'code'       => $code,
                    'client_id'  => $appId,
                ],
            ]
        );
    
        $body = $response->getBody()->getContents();

        $json = \GuzzleHttp\json_decode($body, true);

        if (
            !isset($json['access_token'])
            || !isset($json['expire_in'])
            || !isset($json['refresh_token'])
            || !isset($json['refresh_token_expire_in'])
        ) {
            merror("Core responded with bad response: \n%s", $body);
            throw new AccessDeniedHttpException("Cannot get JWT token from core!");
        }
    
        print_r($json);
        exit;
        

    }
}