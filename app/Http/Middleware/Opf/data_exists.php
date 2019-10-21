<?php

namespace App\Http\Middleware\Opf;

use Closure;
use App\Opf;

class data_exists
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
        $opf    = Opf::where('user_id',$request->id_user)->count();

        if($opf > 0)
        {
            throw new \DataDuplicateExceptions('User sudah menjadi OPF.', 'opf');
        }

        return $next($request);
    }
}
