<?php

namespace App\Http\Controllers\API\Toko\OMercant\Barang\Varian;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helper\Form as FM;

use App\Model\OMerchantBarangVarian;
use App\Model\OMerchantAdmin;
use App\Model\OMerchantBarangGrosir;

use DB;

class Delete extends Controller
{
    public function __invoke(Request $request){
        (new FM)->required($request,[
            'varian_id'
        ]);

        DB::beginTransaction();

        $om_barang_varian   = OMerchantBarangVarian::findOrFail($request->varian_id);

        $cek    = OMerchantBarangGrosir::where('varian_id',$request->varian_id)->first();

        if(!empt($cek)){
            throw new \SecurityExceptions('Varian masih digunakan pada barang grosir.');
        }else{
            if($om_barang_varian->delete()){
                DB::commit();

                return response()->json(['meta' => ['code' => 200,'message' => 'Berhasil delete varian.']]);
            }else{
                DB::rollBack();

                throw new \DataErrorExceptions('Query error','o_merchant_barang_varians');
            }
        }
    }
}
