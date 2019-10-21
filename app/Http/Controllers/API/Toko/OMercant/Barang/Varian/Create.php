<?php

namespace App\Http\Controllers\API\Toko\OMercant\Barang\Varian;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helper\Form as FM;

use App\Model\OMerchantBarangVarian;
use App\Model\OMerchantAdmin;

use DB;

class Create extends Controller
{
    public function __invoke(Request $request){
        (new FM)->required($request,[
            'varian_barang','barang_id'
        ]);

        DB::beginTransaction();

        $om_admin   = OMercantAdmin::where('user_id',$request->user_id)->first();

        if(empty($om_admin)){
            throw new \DataNotFoundExceptions('Data tidak ditemukan.','user');
        }

        $om_barang_varian   = OMerchantBarangVarian::create(array_merge($request->all(),[
            'kode_usaha' => $om_admin->kode
        ]));

        if($om_barang_varian->save()){
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
