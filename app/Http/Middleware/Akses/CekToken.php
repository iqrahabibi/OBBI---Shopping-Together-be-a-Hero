<?php

namespace App\Http\Middleware\Akses;

use Closure;
use DB;
use Auth;

class cekToken
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
        $accessToken    = Auth::user()->token();

        $acces          = DB::table('oauth_access_tokens')
        ->where([
            ['user_id','=',$request->user_id],
            ['revoked','=','0']
        ])->count();

        if($accessToken->user_id != $request->user_id){
            $success['code']    = 230;
            $success['message'] = "You haven't access.";

            return response()->json(['meta'=> $success]);
        }

        // if($acces > 1){
        //     $success['code']    = 240;
        //     $success['message'] = "Permintaan gagal diproses, silahkan logout dan update aplikasi anda di Play Store.";

        //     return response()->json(['meta'=> $success]);
        // }

        return $next($request);
    }
}
