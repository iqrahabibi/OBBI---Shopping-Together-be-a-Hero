<?php

namespace App\Http\Middleware\Akses;

use Closure;
use App\Model\ReferalHerobi;
use App\Model\User;

class CekReferal
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
        

        return $next($request);
    }
}
