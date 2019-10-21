<?php

namespace App\Http\Middleware\Akses;

use Closure;
use App\Model\Log as LG;
use DB;

class Log
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
        if(empty($request->user_id))
        {
            $success['code']    = 400;
            $success['message'] = "User id tidak boleh kosong.";

            return response()->json(['meta'=> $success]);
        }

        $method     = $request->method();
        $parameter  = json_encode($request->all());
        $route      = $request->path();

        $result     = LG::where('user_id',$request->user_id);

        $date       = date('yyyy-mm-dd H:i:s');

        if($result->count() > 1)
        {
            $cek    = LG::where([
                ['method','=',$method],
                ['created_at','=',$date],
                ['route','=',$route],
                ['user_id','=',$request->user_id]
            ])->count();

            if($cek > 0)
            {
                $success['code']    = 235;
                $success['message'] = "too many requests at the same time.";

                return response()->json(['meta'=> $success]);
            }else{
                $cek            = new LG();
                $cek->user_id   = $request->user_id;
                $cek->route     = $route;
                $cek->method    = $method;
                $cek->parameter = $parameter;

                $cek->save();
            }
        }else{
            $cek            = new LG();
            $cek->user_id   = $request->user_id;
            $cek->route     = $route;
            $cek->method    = $method;
            $cek->parameter = $parameter;

            $cek->save();
        }

        return $next($request);
    }
}
