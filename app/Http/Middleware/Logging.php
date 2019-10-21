<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class Logging
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
        Log::channel('api')->debug('REQUEST '.$request);
        return $next($request);
    }

    public function terminate($request, $response){
        Log::channel('api')->debug('RESPONSE '.$response);
    }
}
