<?php

namespace App\Http\Controllers\API\Donasi;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\Donasi;
use App\Model\Saldo;
use App\Model\Kelurahan;

use DB;

class SaldoDonasiDaerah extends Controller
{
    public function __invoke(Request $request){

        $saldo_daerah = DB::table('saldos as a')
        ->join('users as b','a.user_id','=','b.id')
        ->join('detail_users as c','c.user_id','=','b.id')
        ->join('kelurahans as d','c.kelurahan_id','=','d.id')
        ->select(DB::raw('SUM(a.amal) as total'),'c.kelurahan_id','d.id','d.nama_kelurahan')
        ->where('d.id','LIKE','%'.$request->kelurahan_id.'%')
        ->groupBy('kelurahan_id')->get();

        return (new \Data)->respond([
            'access_token'  => array('token'=>$request->header('Authorization')),
            'saldo_daerah' => $saldo_daerah
        ]);
    }
}
