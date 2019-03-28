<?php

/**
 * Created by PhpStorm.
 * User: Baihuzi
 * Date: 2017/8/19
 * Time: 11:20
 */
namespace LianYun\Passport\Middlewares\AccessDecisions;

use Amopi\Mlib\Http\SilexKernel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class PanelApiAccessDecision
{
    /** @var Request */
    public $request;
    /** @var SilexKernel */
    public $application;
    
    public function __construct(Request $request, SilexKernel $application)
    {
        $this->request     = $request;
        $this->application = $application;
    }
    
    public function accessRoles()
    {
        if ($roles = $this->request->attributes->get('allowed-roles')) {
            $roles      = explode(',', $roles);
            $roles      = array_map(
                function ($v) {
                    return trim(strtoupper($v));
                },
                $roles
            );
            $grant_flag = 0;
            foreach ($roles as $role) {
                if ($this->application->isGranted($role)) {
                    $grant_flag = 1;
                }
            }
            
            if ($grant_flag == 0) {
                //throw new AccessDeniedHttpException("Required roles not met, required: " . json_encode($roles));
            }
        }
    }
}
