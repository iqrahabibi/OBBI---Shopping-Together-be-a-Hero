<?php

namespace App\Http\Controllers\API\Report;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Userbalance;
use App\User;

use DB;

class Transaksisaldo extends Controller
{
    public function __invoke(Request $request){
        $transaksi = Userbalance::where('invoice','!=',  null);

        if(!empty($request->input('startdate')) && !empty($request->input('finishdate'))){
            $transaksi = $transaksi->whereDate('created_at','>=',$request->startdate)
            ->whereDate('created_at','<=', $request->finishdate);
        }

        $transaksi = $transaksi->with('user')->get();

        $data['access_token']   = array('token'=>$request->header('Authorization'));
        $data['transaksisaldo'] = $transaksi;
        
        $success['code']    = 200;
        return response()->json(['meta' => $success,'data' => $data]);
    }
}
