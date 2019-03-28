<?php
/**
 * Created by PhpStorm.
 * User: lbc
 * Date: 2016-05-05
 * Time: 14:58
 */

namespace LianYun\Passport\Middlewares;

use Amopi\Mlib\Utils\StringUtils;
use LianYun\Passport\Commons\Fun;
use LianYun\Passport\Commons\Log;
use LianYun\Passport\Middlewares\Renderers\ApiRenderer;
use LianYun\Passport\Middlewares\Renderers\OAuthRenderer;
use LianYun\Passport\Middlewares\Renderers\SdkApiRenderer;
use LianYun\Passport\Passport;
use LianYun\Passport\Middlewares\Renderers\PanelLoginRenderer;
use LianYun\Passport\Middlewares\Renderers\RendererInterface;
use Symfony\Component\HttpFoundation\Request;

class FallbackViewHandler
{
    /**
     * @var Passport
     */
    protected $Api;
    
    public function __construct(Passport $Api)
    {
        $this->Api = $Api;
    }
    
    function __invoke($result, Request $request)
    {
        /** @var RendererInterface $renderer */
        $subdomain = $request->attributes->get('subdomain');
        $logname   = "request";
        switch (true) {
            case StringUtils::stringStartsWith($subdomain, 'oauth'):
                $logname  = '_oauth';
                $renderer = new OAuthRenderer();
                break;
            default:
                $renderer = new ApiRenderer();
                break;
        }
        
        if ($result instanceof CaughtExceptionInfo) {
            $response = $renderer->renderOnException($result);
            Log::save_run_log(
                "Uri:||" .
                $request->getUri()
                . "||"
                . Fun::getIp()
                . "||req:: "
                . $request->getContent()
                . "||res:: "
                . $response->getContent()
                . "||error:: " . json_encode($result)
                . "||request_fail"
                ,
                $logname
            );
            
            return $response;
        }
        else {
            if ($renderer instanceof ApiRenderer
                && ($request->headers->has(ApiRenderer::PASSPORT_GIZIP)
                    || (in_array('gzip', $request->getEncodings(), true)))
            ) {
                $res = $renderer->renderOnGzip($result);
            }
            else {
                $res = $renderer->renderOnSuccess($result);
            }
            Log::save_run_log(
                "sdk_post:||" .
                $request->getUri()
                . "||"
                . Fun::getIp()
                . "||req:: "
                . $request->getContent()
                . "||res:: "
                . $res->getContent()
                . "||request_success"
                ,
                $logname
            );
            
            return $res;
            
        }
        
    }
}
