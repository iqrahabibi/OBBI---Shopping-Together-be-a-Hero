<?php

namespace App\Http\Middleware\Akses;

use Closure;
use App\Userbalance;

class checkSaldoPaid
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
            $success['message'] = "Parameter user id tidak boleh kosong.";

            return response()->json(['meta'=> $success]);
        }

        if(empty($request->email_cust))
        {
            $success['code']    = 400;
            $success['message'] = "Parameter email tujuan tidak boleh kosong.";

            return response()->json(['meta'=> $success]);
        }

        if(empty($request->saldo))
        {
            $success['code']    = 400;
            $success['message'] = "Parameter saldo tidak boleh kosong.";

            return response()->json(['meta'=> $success]);
        }

        return $next($request);
    }
}
