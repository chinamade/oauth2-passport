<?php
/**
 * Created by PhpStorm.
 * User: lbc
 * Date: 2016-05-05
 * Time: 14:57
 */

namespace LianYun\Passport\Middlewares;

use LianYun\Passport\Exceptions\ErrorCodeHandler;
use Amopi\Mlib\Utils\Exceptions\DataValidationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ExceptionHandler
{
    
    function __invoke(\Exception $e, Request $request, $httpStatusCode)
    {
        mtrace($e, "Fallback handling exception: ");
        $caughtException = new CaughtExceptionInfo($e, $httpStatusCode);
        $this->furtherProcessException($caughtException, $e);
        
        return $caughtException;
    }
    
    protected function furtherProcessException(CaughtExceptionInfo $info, \Exception $e)
    {
    switch (true) {
            case ($e instanceof DataValidationException):
                
                $info->setCode(Response::HTTP_BAD_REQUEST);
                $info->setAttribute('key', $e->getFieldName());

                break;
            case ($e instanceof HttpException): {
                $info->setErrorCode($e->getCode());
                break;
            }
            default:
               // $info->setErrorCode(1000000);
            
        }
    }
    
}
