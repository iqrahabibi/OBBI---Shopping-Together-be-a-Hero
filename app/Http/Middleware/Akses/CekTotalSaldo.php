<?php

namespace App\Http\Middleware\Akses;

use App\Model\Saldo;

use Closure;

class CekTotalSaldo
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
        $total = Saldo::sum('saldo');

        if($total > 2000000000){
            throw new \DataErrorExceptions('Tidak dapat melakukan isi saldo, silahkan lakukan beberapa saat lagi.','saldos');
        }

        return $next($request);
    }
}
