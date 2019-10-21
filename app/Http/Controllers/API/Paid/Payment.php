<?php

namespace App\Http\Controllers\API\Paid;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helper\Form as FM;
use Illuminate\Support\Facades\Hash;

use App\Model\DigiPay;
use App\Model\User;
use App\Model\Saldo;

use Auth;
use DB;

class Payment extends Controller
{
    public function __invoke(Request $request)
    {
        (new FM)->required($request,[
            'user_id','invoice','password'
        ]);

        $user   = User::where('id',$request->user_id)->first();

        if(!(Hash::check($request->password,$user->password))){
            throw new \SecurityExceptions('Password tidak sesuai.');
        }

        DB::beginTransaction();

      
        $tagihan    = DigiPay::where('invoice',$request->invoice)->where('valid',0)->first();

        if(empty($tagihan))
        {
            throw new \DataNotFoundExceptions('Data tidak ditemukan.','digipays');
        }
        
        $balance    = Saldo::where('user_id',$request->user_id)->first();

        if($tagihan->id == $balance->user_id){
            throw new \SecurityExceptions('Tidak bisa bayarin diri sendiri.');
        }

        if($balance->saldo < $tagihan->jumlah)
        {
            throw new \SecurityExceptions('Saldo Anda tidak cukup.');
        }else if($balance->saldo < 0)
        {
            throw new \SecurityExceptions('Saldo Anda minus.');
        }

        $notes = "";

        if($request->input('notes'))
        {
            $notes  = $request->notes;
        }else{
            $notes     = "Top up orang lain";
        }
        
        $result = new DigiPay();
        $result->user_id    = $request->user_id;
        $result->trxid      = $request->invoice;
        $result->awal       = $balance->saldo;
        $result->jumlah     = $tagihan->jumlah;
        $result->akhir      = $balance->saldo - $tagihan->jumlah;
        $result->kode       = 0;
        $result->valid      = 1;
        $result->notes      = $notes;

        $result->save();

        $result2    = DigiPay::where('invoice',$request->invoice)->update(['valid' => 1,'kode' => 0]);

        $jumlah_saldo = $balance->saldo - $tagihan->jumlah;

        $saldo  = Saldo::updateOrCreate(
            ['user_id' => $request->user_id],
            ['saldo' => $jumlah_saldo,'amal' => $balance->amal,'keuntungan'=> $balance->keuntungan]
        );

        $cek_saldo = Saldo::where('user_id',$tagihan->user_id);

        if(!empty($cek_saldo->first())){
            $totalsaldo = $cek_saldo->first()->saldo + $tagihan->jumlah;
            $totalkeuntungan = $cek_saldo->first()->keuntungan;
            $totalamal      = $cek_saldo->first()->amal;

            $cek_saldo = $cek_saldo->update(['saldo' => $totalsaldo, 'keuntungan' => $totalkeuntungan,'amal' => $totalamal]);
        }else{
            $insert = new Saldo();
            $insert->user_id    = $tagihan->user_id;
            $insert->saldo      = $tagihan->jumlah;
            $insert->keuntungan = 0;
            $insert->amal       = 0;

            $insert->save();
        }
        
        if($result->save()){
            DB::commit();

            $success['code']        = 200;
            $success['message']     = "Berhasil bayarin orang.";
            
            return response()->json(['meta'=> $success]);
        }else{
            DB::rollBack();
            $success['code']    = 400;
            $success['message'] = "Gagal bayarin orang.";

            return response()->json(['meta'=> $success]);
        }
    }
}
