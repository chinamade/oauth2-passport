<?php
/**
 * Created by PhpStorm.
 * User: qiudaoyu
 * Date: 2019/1/20
 * Time: 12:20 PM
 */

namespace LianYun\Passport\Middlewares\Renderers;

use LianYun\Passport\Exceptions\ErrorCodeHandler;
use LianYun\Passport\Middlewares\CaughtExceptionInfo;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ServerApiRenderer implements RendererInterface
{
    
    public function __construct()
    {
    
    }
    
    /**
     * Take the unformatted result and return a Response object
     *
     * @param $result
     *
     * @return JsonResponse
     */
    public function renderOnSuccess($result)
    {
        if (!is_array($result)) {
            $result = ['result' => $result];
        }
        $resultHandler = array_merge(
            [
                'status' => 'ok',
            ],
            $result
        );
        return new JsonResponse($resultHandler);
    }
    
    /**
     * Take the caught exception info object and return a Response object
     *
     * @param CaughtExceptionInfo $info
     *
     * @return JsonResponse
     */
    public function renderOnException(CaughtExceptionInfo $info)
    {
        if ($info->getErrorCode() == ErrorCodeHandler::SDK_VALIDATED_DATA_ERROR) {
            $code = '-2';
            $msg  = $info->getException()->getMessage() . " Key:" . $info->getAttribute('key');
        }
        else {
            $msg  = $info->getException()->getMessage();
            $code = $info->getErrorCode();
        }
        
        $resultHandler = [
            'status'  => 'fail',
            'error'   => $code,
            'err_msg' => $msg,
        ];
        
        $response = new Response(json_encode($resultHandler), 200, ['Content-Type' => 'application/json']);
        $response->setStatusCode(Response::HTTP_OK);

        return $response;
    }
}