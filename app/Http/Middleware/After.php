<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class After
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
        $response   = $next($request);
        $original = Log::info("Log Response : ",$response->getOriginalContent());
        return $response;
    }
}
