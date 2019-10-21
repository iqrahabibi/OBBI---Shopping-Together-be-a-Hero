<?php

namespace App\Http\Controllers\API\Belanja\Cart;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helper\Form as FM;

use App\Model\CartOnlineDetailNasional;
use App\Model\CartOnlineDetailDaerah;
use App\Model\CartOnline;
use App\Model\OMerchantBarangInventory;
use App\Model\OMerchantBarangGrosir;
use App\Model\BarangInventory;
use App\Model\BarangNasional;

use DB;

class Delete extends Controller
{
    public function __invoke(Request $request){
        (new FM)->required($request,[
            'data'
        ]);

        DB::beginTransaction();

        $data_cart  = $request->data;

        if(sizeof($data_cart['belanja']) == 0){

            if($data_cart['type'] == 'lokal'){

                $cart = CartOnline::where([
                    ['user_id','=',$request->user_id],
                    ['flag','=',2],
                    ['status','=','waiting']
                ])->delete();
            }else if($data_cart['type'] == 'nasional'){
                $cart = CartOnline::where([
                    ['user_id','=',$request->user_id],
                    ['flag','=',1],
                    ['status','=','waiting']
                ])->delete();
            }

            DB::commit();

            return response()->json(['meta' => ['code' => 200,'message' => 'Berhasil hapus data']]);
        }

        foreach($data_cart['belanja'] as $key => $value){

            if($data_cart['type'] == 'lokal'){

                $cek_harga  = CartOnlineDetailDaerah::where([
                    ['barang_id','=',$value['barang_id']],
                    ['kode','=',$value['kode_usaha']],
                    ['varian_id','=',$value['varian']]
                ])->first();

                $inventory  = OMerchantBarangInventory::where([
                    ['barang_id','=',$value['barang_id']],
                    ['kode_usaha','=',$value['kode_usaha']]
                ])->first();

                $inventory->qty = $inventory->qty + $cek_harga->qty;
                $inventory->update();

                $cek_harga->delete();
                
            }elseif ($data_cart['type'] == 'nasional') {

                $cek_harga  = CartOnlineDetailNasional::where([
                    ['barang_id','=',$value['barang_id']],
                    ['gudang_id','=',$value['gudang_id']],
                    ['varian_id','=',$value['varian']]
                ])->first();

                $inventory  = BarangInventory::where([
                    ['barang_id','=',$value['barang_id']],
                    ['gudang_id','=',$value['gudang_id']]
                ])->first();

                $inventory->qty = $inventory->qty + $cek_harga->qty;
                $inventory->update();

                $cek_harga->delete();
            }
        }

        if($data_cart['type'] == 'lokal'){
            $cek_cart = CartOnline::with('detail_daerah')->where('id', $request->cart_id)->first();
            if(sizeof($cek_cart->detail_daerah) == 0){
                $cek_cart->delete();
            }
        }elseif ($data_cart['type'] == 'nasional') {
            $cek_cart = CartOnline::with('detail_nasional')->where('id', $request->cart_id)->first();
            if(sizeof($cek_cart->detail_nasional) == 0){
                $cek_cart->delete();
            }
        }

        DB::commit();

        $success['code']    = 200;
        $success['message'] = "Data berhasil dihapus dari keranjang.";

        return response()->json(['meta' => $success]); 
        // }else{
        //     DB::rollBack();
        //     throw new \SecurityExceptions('Data gagal di hapus dari keranjang.');
        // }
    }
}
