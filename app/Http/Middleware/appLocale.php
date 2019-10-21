<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class appLocale {
    public function handle (Request $request, Closure $next, $guard = null) {
        if ( $request->input('lang') == "" ) {
            app()->setLocale('id');
        } else {
            app()->setLocale($request->input('lang'));
        }

        return $next($request);
    }
}
