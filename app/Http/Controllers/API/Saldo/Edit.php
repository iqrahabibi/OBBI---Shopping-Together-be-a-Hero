<?php

namespace App\Http\Controllers\API\Saldo;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helper\Form as FM;

use App\Model\DigiPay;

use DB;

class Edit extends Controller
{
    public function __invoke(Request $request){
        // (new FM)->required($request,[
        //     'balance_id','updated_at','saldo','notes'
        // ]);
        
        // DB::beginTransaction();

        // $update    = DigiPay::where('id',$request->balance_id)->first();


        // if(!empty($update)){
        //     $update->jumlah = $request->saldo;
        //     $update->notes  = $request->notes;

        //     $update->save();

        //     DB::commit();

        //     return (new \Data)->respond([
        //         'access_token'  => $request->header('Authorization'),
        //         'digi_pay' => $update
        //     ]);
        // }else {
        //     return ["meta"=>["code"=>500, "message"=>"Data tidak ditemukan."]];
        //     //throw new \DataNotFoundExceptions('Data tidak ditemukan.','digi_pays');
        // }
    }
}
