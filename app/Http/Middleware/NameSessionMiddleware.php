<?php

namespace App\Http\Middleware;

use Closure;

class NameSessionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if($request->session()->has('name')){
            //session中存在name，说明用户已经登录
            
            return $next($request);
        }else{
            return redirect('/');
        }
        
    }
}
