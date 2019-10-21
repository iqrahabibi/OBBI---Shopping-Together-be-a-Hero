<?php

namespace App\Http\Controllers\API\Belanja\Cart;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helper\Form as FM;
use App\OBBI\obbiHelper as DATA;

use App\Model\CartOnline;
use App\Model\CartOnlineDetailDaerah;
use App\Model\CartOnlineDetailNasional;
use App\Model\BarangInventory;
use App\Model\OMerchantBarangInventory;
use App\Model\BarangNasional;
use App\Model\OMerchantBarangGrosir;

use DB;
use Auth;

class Create extends Controller
{
    public function __invoke(Request $request){
        (new FM)->required($request,[
            'data'
        ]);

        DB::beginTransaction();

        $data_cart  = $request->data;
        // $data_cart  = json_decode($request->data,true); // Testing via Postman

        $total_harga = 0;
        $total_qty  = 0;
        $inventory_qty = 0;

        $flag    = false;
        
        $total_qty_lokal = 0;
        $total_belanja_lokal = 0;

        $total_qty_nasional = 0;
        $total_belanja_nasional = 0;

        $harga  = 0;

        if ($data_cart['type'] == 'nasional') {
            
            $cek_harga    = BarangNasional::with('varian')
            ->where([
                ['barang_id','=',$data_cart['belanja']['barang_id']],
                ['gudang_id','=',$data_cart['belanja']['gudang_id']],
                ['varian_id','=',$data_cart['belanja']['varian']]
            ])->orderBy('qty','desc')->get();

            foreach($cek_harga as $key_harga => $value_harga){
                if($data_cart['belanja']['qty'] % $value_harga['qty'] == 0){
                    
                    $harga  = $value_harga->harga_jual;
                    break;
                }
            }

            if($harga == 0){
                throw new \SecurityExceptions('Quantity tidak sesuai penjualan.');
            }

            $cart   = CartOnline::updateOrCreate([
                'user_id'       => $request->user_id,
                'flag'          => 1,
                'status'        => 'waiting'
            ],[
                'total_belanja' => 0,
                'total_qty'     => 0,
            ]);

            $cart_detail    = CartOnlineDetailNasional::updateOrCreate([
                'cart_id'       => $cart->id,
                'barang_id'     => $data_cart['belanja']['barang_id'],
                'varian_id'     => $data_cart['belanja']['varian'],
                'gudang_id'     => $data_cart['belanja']['gudang_id'],
            ],[
                'harga'         => $harga * $data_cart['belanja']['qty'],
                'qty'           => $data_cart['belanja']['qty'],
                'status'        => "waiting"
            ]);

            $inventory  = BarangInventory::where('id',$data_cart['belanja']['belanja_id'])->first();
            if($inventory->qty < $cart_detail->qty){
                throw new \SecurityExceptions('Quantity melebihi stok yang tersedia.');
            }
            $inventory->qty = $inventory->qty - $cart_detail->qty;
            $inventory->update();


            $cari_detail_terakhir = CartOnlineDetailNasional::where([
                'cart_id'       => $cart->id
            ]);

            $cart->total_belanja = $cari_detail_terakhir->sum('harga');
            $cart->total_qty = $cari_detail_terakhir->sum('qty');

        }else if($data_cart['type'] == 'lokal'){

            $cek_harga    = OMerchantBarangGrosir::with('varian')
            ->where([
                ['barang_id','=',$data_cart['belanja']['barang_id']],
                ['kode_usaha','=',$data_cart['belanja']['kode_usaha']],
                ['varian_id','=',$data_cart['belanja']['varian']]
            ])->orderBy('qty','desc')->get();

            foreach($cek_harga as $key_harga => $value_harga){
                if($data_cart['belanja']['qty'] % $value_harga['qty'] == 0){
                    
                    $harga  = $value_harga->harga_jual;
                    break;
                }
            }

            if($harga == 0){
                throw new \SecurityExceptions('Quantity tidak sesuai penjualan.');
            }

            $cart   = CartOnline::updateOrCreate([
                'user_id'       => $request->user_id,
                'flag'          => 2,
                'status'        => 'waiting'
            ],[
                'total_belanja' => 0,
                'total_qty'     => 0,
            ]);

            $cart_detail    = CartOnlineDetailDaerah::updateOrCreate([
                'cart_id'       => $cart->id,
                'barang_id'     => $data_cart['belanja']['barang_id'],
                'varian_id'     => $data_cart['belanja']['varian'],
                'kode'          => $data_cart['belanja']['kode_usaha'],
            ],[
                'harga'         => $harga * $data_cart['belanja']['qty'],
                'qty'           => $data_cart['belanja']['qty'],
                'status'        => "waiting"
            ]);

            $inventory  = OMerchantBarangInventory::where('id',$data_cart['belanja']['belanja_id'])->first();
            if($inventory->qty < $cart_detail->qty){
                throw new \SecurityExceptions('Quantity melebihi stok yang tersedia.');
            }
            $inventory->qty = $inventory->qty - $cart_detail->qty;
            $inventory->update();

            $cari_detail_terakhir = CartOnlineDetailDaerah::where([
                'cart_id'       => $cart->id
            ]);

            $cart->total_belanja = $cari_detail_terakhir->sum('harga');
            $cart->total_qty = $cari_detail_terakhir->sum('qty');
        }

        if($cart->save()){
            DB::commit();

            $success['code']    = 200;
            $success['message'] = "Data berhasil ditambahkan ke dalam keranjang.";

            if($data_cart['type'] == "nasional"){
                $arrays['cart']      = CartOnline::with('detail_nasional')->where('id',$cart->id)->first();
            }else{
                $arrays['cart']      = CartOnline::with('detail_daerah')->where('id',$cart->id)->first();
            }

            return response()->json(['meta' => $success,'data' => $arrays]); 

        }else{
            DB::rollBack();

            throw new \SecurityExceptions('Data gagal di tambahkan ke keranjang.');
        }
    }
}
