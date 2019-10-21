<?php

namespace App\Http\Controllers\API\Saldo;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helper\Form as FM;

use App\Model\DigiPay;
use App\Model\User;
use App\Model\Saldo;

use DB;

class Grant extends Controller
{
    public function __invoke(Request $request){
        (new FM)->required($request,[
            'balance_id','updated_at','valid'
        ]);

        DB::beginTransaction();
        
        $cek_transaksi  = DigiPay::where([
            ['id','=', $request->balance_id],
            ['updated_at','=', $request->updated_at]
        ])->first();

        if(!empty($cek_transaksi)){

            if($request->valid == 0){

                $cek_transaksi->valid   = 1;
                $cek_transaksi->kode    = 0;
                $cek_transaksi->akhir   = $cek_transaksi->jumlah;

                $cek_transaksi->save();

                $saldo = Saldo::where('user_id',$cek_transaksi->user_id);

                if($saldo->first()->saldo > 2000000){
                    return ["meta"=>["code"=>500, "message"=>"Saldo tidak boleh lebih dari Rp2.000.000"]];
                    //throw new \DataErrorExceptions('Saldo tidak boleh lebih dari Rp2.000.000','saldo');
                }

                if(!empty($saldo->first())){
                    $totalsaldo = $saldo->first()->saldo + $cek_transaksi->jumlah+$cek_transaksi->kode;
                    $totalkeuntungan = $saldo->first()->keuntungan;
                    $totalamal      = $saldo->first()->amal;

                    $saldo = $saldo->update(['saldo' => $totalsaldo, 'keuntungan' => $totalkeuntungan,'amal' => $totalamal]);

                }else{
                    $insert = new Saldo();
                    $insert->user_id    = $cek_transaksi->user_id;
                    $insert->saldo      = $cek_transaksi->jumlah+$cek_transaksi->kode;
                    $insert->keuntungan = 0;
                    $insert->amal       = 0;

                    $insert->save();
                }

                DB::commit();

                return (new \Data)->respond([
                    'access_token'  => array('token'=>$request->header('Authorization')),
                    'transaksi' => $cek_transaksi
                ]);

            }else{

                $cek_transaksi->valid   = 0;
                $cek_transaksi->kode    = 0;

                $cek_transaksi->save();

                DB::commit();

                return (new \Data)->respond([
                    'access_token'  => array('token'=>$request->header('Authorization')),
                    'transaksi' => $cek_transaksi
                ]);

            }

        }else{
            return ["meta"=>["code"=>500, "message"=>"Data tidak ditemukan."]];
            //throw new \DataNotFoundExceptions('Data tidak ditemukan.','digi_pay');
        }
    }
}
