<?php

namespace App\Http\Middleware\Opf;

use Closure;
use App\User;

class user_type
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
        $user_type  = User::where('id',$request->id_user)->first();

        if($user_type == null)
        {
            throw new \SecurityExceptions('User not found.');
        }

        if($user_type->user_type == 2)
        {
            throw new \SecurityExceptions('Hanya Herobi yang bisa menjadi OPF.');
        }

        return $next($request);
    }
}
