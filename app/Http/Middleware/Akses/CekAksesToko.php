<?php

namespace App\Http\Middleware\Akses;

use Closure;

use App\Model\OmerchantAdmin;

class CekAksesToko
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
        $cek = OMerchantAdmin::with('usaha_o_merchant')->where('user_id',$request->user_id)->first();

        if(empty($cek->usaha_o_merchant)){
            throw new \SecurityExceptions('Anda tidak memiliki akses toko.');
        }

        return $next($request);
    }
}
