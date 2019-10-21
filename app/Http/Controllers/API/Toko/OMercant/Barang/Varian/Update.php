<?php

namespace App\Http\Controllers\API\Toko\OMercant\Barang\Varian;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helper\Form as FM;

use App\Model\OMerchantBarangVarian;
use App\Model\OMerchantAdmin;

use DB;

class Update extends Controller
{
    public function __invoke(Request $request){
        (new FM)->required($request,[
            'varian_barang','barang_id','varian_id'
        ]);

        DB::beginTransaction();

        $om_barang_varian   = OMerchantBarangVarian::findOrFail($request->varian_id);
        $om_barang_varian->varian_barang    = $request->varian_barang;
        $om_barang_varian->barang_id        = $request->barang_id;

        if($om_barang_varian->update()){
            DB::commit();

            return (new \Data)->respond([
                'access_token'  => array('token'=>$request->header('Authorization')),
                'om_barang_varian' => $om_barang_varian,
            ]);
        }else{
            DB::rollBack();

            throw new \DataErrorExceptions('Query error','o_merchant_barang_varians');
        }
    }
}
