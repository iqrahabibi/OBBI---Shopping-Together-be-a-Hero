<?php

namespace App\Http\Controllers\API\Belanja;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\Checkout;
use App\Model\User;

use DB;
use Auth;

class ReadInvoice extends Controller
{
    public function __invoke(Request $request){
        DB::beginTransaction();

        $data   = Checkout::with('alamat.kelurahan.kecamatan.kota.provinsi')
        ->whereHas('alamat',function($query) use ($request){
            $query->whereHas('user',function($query2) use ($request){
                $query2->where('id',$request->user_id);
            });
        });

        if($request->input('checkout_id')){
            $data   = $data->where('id',$request->checkout_id);
        }

        $data   = $data->get();

        $array  = array();
        $status = "";

        foreach($data as $key => $value){

            if($value->status == "done"){
                $cek = Checkout::with('cart.detail_daerah','cart.detail_nasional')
                ->whereHas('alamat',function($query) use ($request){
                    $query->whereHas('user',function($query2) use ($request){
                        $query2->where('id',$request->user_id);
                    });
                })
                ->where('tipe_belanja',$value->tipe_belanja)
                ->where('status','done')->get();

                foreach($cek as $key2 => $value2){

                    // dd($value2->cart->detail_nasional);
                    
                    if($value2->tipe_belanja == "lokal"){
                        foreach($value2->cart->detail_daerah as $key3 => $value3){

                            $status = $value3->status;
                        }
                    }else if($value2->tipe_belanja == "nasional"){
                        foreach($value2->cart->detail_nasional as $key3 => $value3){

                            $status = $value3->status;
                        }
                    }
                }
            }else{

                $status = $value->status;
            }

            $array[$key]['expired_at'] = $value->expired_at;
            $array[$key]['invoice'] = $value->invoice;
            $array[$key]['status']  = $status;
            $array[$key]['alamat']  = $value->alamat;
            $array[$key]['tipe_pembayaran'] = $value->tipe_pembayaran;
            $array[$key]["total_belanja"] = $value->total_belanja;
            $array[$key]['phone']  = $value->alamat->phone;

        }

        DB::commit();

        $success['code']    = 200;
        $success['message'] = "";

        $array2['show_invoice'] = $array;

        return response()->json([
            "meta" => $success,
            "data" => $array2
        ]);
    }
}
