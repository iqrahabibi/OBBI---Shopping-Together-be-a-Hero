<?php

namespace App\Http\Middleware\Akses;

use Closure;
use App\Model\Log as LG;
use DB;
use Auth;

class LogWeb
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

        $method     = $request->method();
        $parameter  = json_encode($request->all());
        $route      = $request->path();

        if(!($route == '/' || $route == 'login' || $route == 'logout' || $route == 'password/reset')){
            if(empty(Auth::user()->id))
            {
                $success['code']    = 400;
                $success['message'] = "User id tidak boleh kosong.";

                return response()->json(['meta'=> $success]);
            }
        }

        if($method == 'GET' || $route == '/' || $route == 'login' || $route == 'logout'){
            return $next($request);
        }

        $result     = LG::where('user_id',Auth::user()->id);

        $date       = date('yyyy-mm-dd H:i:s');

        if($result->count() > 1)
        {
            $cek    = LG::where([
                ['method','=',$method],
                ['created_at','=',$date],
                ['route','=',$route],
                ['user_id','=',Auth::user()->id]
            ])->count();

            if($cek > 0)
            {
                $success['code']    = 235;
                $success['message'] = "too many requests at the same time.";

                return response()->json(['meta'=> $success]);
            }else{
                $cek            = new LG();
                $cek->user_id   = Auth::user()->id;
                $cek->route     = $route;
                $cek->method    = $method;
                $cek->parameter = $parameter;

                $cek->save();
            }
        }else{
            $cek            = new LG();
            $cek->user_id   = Auth::user()->id;
            $cek->route     = $route;
            $cek->method    = $method;
            $cek->parameter = $parameter;

            $cek->save();
        }

        return $next($request);
    }
}
