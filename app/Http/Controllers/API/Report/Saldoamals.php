<?php

namespace App\Http\Controllers\API\Report;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Saldoamal;
use App\Detail;

use DB;

class Saldoamals extends Controller
{
    public function __invoke(Request $request)
    {
        $saldo   = new Saldoamal();

        if($request->input('startdate') && $request->input('finishdate')){
            $saldo = $saldo->whereDate('created_at','>=',$request->startdate)
            ->whereDate('created_at','<=', $request->finishdate);
        }

        $saldo = $saldo->with('user')->get();

        $data['access_token']   = array('token'=>$request->header('Authorization'));
        $data['saldoamal']      = $saldo;
        
        $success['code']    = 200;
        return response()->json(['meta' => $success,'data' => $data]);
    }
}