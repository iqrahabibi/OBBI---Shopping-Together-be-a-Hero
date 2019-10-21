<?php

namespace App\Http\Middleware\Akses;

use Closure;
use App\Model\DetailUser;

class CekDetailUser
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
        $detail_user = DetailUser::where([
            ['user_id','=',$request->user_id],
            ['valid','=',1]
        ])->first();

        if(empty($detail_user)){
            $success['code']    = 404;
            $success['message'] = "Silahkan lengkapi data diri Anda..";

            return response()->json(['meta' => $success]);
        }
        
        return $next($request);
    }
}
