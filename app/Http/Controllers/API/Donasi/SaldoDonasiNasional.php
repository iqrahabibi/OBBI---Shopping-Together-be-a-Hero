<?php

namespace App\Http\Controllers\API\Donasi;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\Saldo;

class SaldoDonasiNasional extends Controller
{
    public function __invoke(Request $request){
        $saldo  = Saldo::sum('amal');

        return (new \Data)->respond([
            'access_token'  => array('token'=>$request->header('Authorization')),
            'saldo_nasional' => $saldo
        ]);
    }
}
