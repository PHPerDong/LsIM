<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;


class UserAuth
{
	
	protected $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }
    
    
    public function handle($request, Closure $next)
    {
    	
    	//$this->auth->shouldUse($guard);
    	//未登录的，登录
        if (!$this->auth->check()) {
            if ($request->isMethod('ajax')) {
                return ['code'=>1,'msg'=>'登录已过期，请重新登录'];
            } else {
                return redirect(route('login'));
            }
        }
        
        return $next($request);
        
    }
	
	protected $except = [
        //
    ];
	
}