<?php
/**
 * Created by PhpStorm.
 * User: lbc
 * Date: 2016-06-16
 * Time: 16:56
 */

namespace LianYun\Passport\Middlewares\Renderers;

use LianYun\Passport\Middlewares\CaughtExceptionInfo;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiRenderer implements RendererInterface
{
    
    const PASSPORT_GIZIP = 'passport-gzip';
    
    protected $type;
    
    public function __construct($type = "api")
    {
        $this->type = $type;
    }
    
    

    
    public function renderOnGzip($result)
    {
        if (!is_array($result)) {
            $result = ['result' => $result];
        }
        $result = gzencode(json_encode($result), 9);
        
        return new Response($result, 200, ['Content-Encoding' => 'gzip', 'Content-type' => 'application/json']);
    }
    
    
    
    /**
     * Take the unformatted result and return a Response object
     *
     * @param $result
     *
     * @return Response
     */
    public function renderOnSuccess($result)
    {
        switch ($this->type) {
            
            default:
                if (!is_array($result)) {
                    $result = ['result' => $result];
                }
                
                return new JsonResponse($result);
        }
        
    }
    
    /**
     * Take the caught exception info object and return a Response object
     *
     * @param CaughtExceptionInfo $info
     *
     * @return Response
     */
    public function renderOnException(CaughtExceptionInfo $info)
    {
        switch ($this->type) {
            default:
                $error_code = intval($info->getCode());
                if ($error_code < 200) {
                    $error_code = 403;
                }
                
                return new JsonResponse(
                    [
                        'status'    => 'error',
                        'code'      => $error_code,
                        'error_msg'=>$info->getException()->getMessage(),
                        /*'exception' => [
                            'type'    => $info->getShortExceptionType(),
                            'message' => $info->getException()->getMessage(),
                            'file'    => $info->getException()->getFile(),
                            'line'    => $info->getException()->getLine(),
                        ],
                        'extra'     => $info->getAttributes(),*/
                    ],
                    $error_code
                );
        }
    }
}
