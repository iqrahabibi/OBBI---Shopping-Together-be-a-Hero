<?php

namespace App\Http\Middleware\Akses;

use Closure;
use Cookie;

class CheckCookie
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
        // if($request->hasCookie('token')){
        //     return $next($request);
        // }else{
        //     return redirect('/login');
        // }
        return $next($request);
    }
}
