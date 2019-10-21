<?php

namespace App\Http\Controllers\API\Belanja\Cart;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\CartOnline;

use DB;

class Read extends Controller
{
    public function __invoke(Request $request){
        $cart   = CartOnline::with('detail_nasional.barang.gambar',
        'detail_daerah.barang.om_gambar','detail_nasional.varian_nasional',
        'detail_daerah.varian_daerah','detail_daerah.usaha_om.usaha',
        'detail_nasional.gudang')->where([
            ['user_id','=',$request->user_id],
            ['status','=','waiting']
        ])->get();

        $array  = array(); 
        $barang_daerah      = null;
        $barang_nasional    = null;

        foreach($cart as $key => $value){
            if(!empty($value->flag == 1)){
                // $barang_nasional['barang_nasional'] = $value;
                $barang_nasional['cart_id']         = $value->id;
                $barang_nasional['total_belanja']   = $value->total_belanja;
                $barang_nasional['status']          = $value->status;
                $barang_nasional['total_qty']       = $value->total_qty;
                
                $array2 = array();
                foreach($value->detail_nasional as $key2 => $value2){
                    $gambar = array();
                    $array2[$key2]['nama_barang']   = $value2->barang->nama_barang;
                    $array2[$key2]['barang_id']     = $value2->barang->id;
                    $array2[$key2]['weight']        = $value2->barang->weight;
                    $array2[$key2]['brand']         = $value2->barang->brand;
                    $array2[$key2]['qty']           = $value2->qty;
                    $array2[$key2]['harga']         = $value2->harga;
                    $array2[$key2]['varian']        = $value2->varian_nasional->varian_barang;
                    $array2[$key2]['varian_id']     = $value2->varian_nasional->id;
                    $array2[$key2]['gudang_id']     = $value2->gudang_id;
                    $array2[$key2]['nama_gudang']   = $value2->gudang->nama_gudang;
                   
                    foreach($value2->barang->gambar as $key3 => $value3){
                        $gambar[$key3]    = config('app.api').'/storage'.$value3->gambar_barang;
                    }

                    $array2[$key2]['gambar']   = $gambar;
                }
                
                $barang_nasional['detail_nasional'] = $array2;

            }else if(!empty($value->flag == 2)){
                // $barang_daerah['barang_daerah'] = $value;
                $barang_daerah['cart_id']           = $value->id;
                $barang_daerah['total_belanja']     = $value->total_belanja;
                $barang_daerah['status']            = $value->status;
                $barang_daerah['total_qty']         = $value->total_qty;

                $array3 = array();
                
                foreach($value->detail_daerah as $key2 => $value2){
                    $gambar = array();
                    $array3[$key2]['nama_barang']   = $value2->barang->nama_barang;
                    $array3[$key2]['barang_id']     = $value2->barang->id;
                    $array3[$key2]['weight']        = $value2->barang->weight;
                    $array3[$key2]['brand']         = $value2->barang->brand;
                    $array3[$key2]['qty']           = $value2->qty;
                    $array3[$key2]['harga']         = $value2->harga;
                    $array3[$key2]['varian']        = $value2->varian_daerah->varian_barang;
                    $array3[$key2]['varian_id']     = $value2->varian_daerah->id;
                    $array3[$key2]['kode_usaha']    = $value2->kode;
                    $array3[$key2]['nama_usaha']    = $value2->usaha_om->usaha->nama_usaha;

                   

                    foreach($value2->barang->om_gambar as $key3 => $value3){
                        $gambar[$key3]    = config('app.api').'/storage'.$value3->gambar_barang;
                    }
                    $array3[$key2]['gambar'] = $gambar;
                }

                $barang_daerah['detail_daerah'] = $array3;
            }
        }
        $array['barang_nasional']   = $barang_nasional;
        $array['barang_daerah']     = $barang_daerah;

        $success['code']    = 200;
        $success['message'] = 'Data keranjang belanja.';

        return response()->json(['meta' => $success,'data' => $array]);
    }
}
