<?php
/**
 * Created by PhpStorm.
 * User: Baihuzi
 * Date: 2019/3/21
 * Time: 15:27
 */

namespace LianYun\Passport\Commons;

use Lcobucci\JWT\Parser;
use Lcobucci\JWT\ValidationData;
use LianYun\Passport\Passport;
use Symfony\Component\HttpFoundation\Request;

class CompanyHelper
{
    
    /**
     * @param Passport $passport
     * @param Request  $request
     *
     * @return int
     */
    public function checkIsCompany(Passport $passport, Request $request)
    {
        if ($passport->getParameter('app.is_debug')) {
            return 1;
        }
        else {
            return 0;
        }
    }
    
    public function validationToken($jwtString, $ip = '')
    {

        $parser = new Parser();
        try {
            $jwtToken = $parser->parse($jwtString);
        } catch (\Exception $e) {
            mdebug("Malformed JWT Token: %s", $e->getMessage());
            
            return false;
        }
        try {
            $userId     = $jwtToken->getClaim('uid');
            $loggedInIp = $jwtToken->getClaim('ip');
        } catch (\OutOfBoundsException $e) {
            return false;
        }
        
        if ($loggedInIp !== '' && $ip != $loggedInIp) {
            $msg = sprintf(
                "IP mismatch for user %d, authenticated ip = %s, current ip = %s",
                $userId,
                $loggedInIp,
                $ip
            );
            mwarning(
                $msg
            );
            
            return $msg;
        }
        
        $validationData = new ValidationData();
        $validationData->setIssuer(Passport::app()->getParameter('app.token_iss'));
        if (!$jwtToken->validate($validationData)) {
            mdebug("JWT Token failed validation.");
            
            return false;
        }
        
        return true;
        
    }
}