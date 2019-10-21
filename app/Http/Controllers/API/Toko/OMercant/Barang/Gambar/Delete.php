<?php

namespace App\Http\Controllers\API\Toko\OMercant\Barang\Gambar;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\OBBI\obbiHelper;

use App\Model\OMerchantBarangVarian;
use App\Model\OMerchantAdmin;

use DB;
use File;

class Delete extends Controller
{
    public function __invoke(Request $request){
        (new FM)->required($request,[
            'gambar_id'
        ]);

        DB::beginTransaction();

        $om_barang_gambar = OMerchantBarangGambar::findOrFail($request->gambar_id);
        
        if(!empty($om_barang_gambar->barang_gambar)){
            File::delete(obbiHelper::storage($om_barang_gambar->barang_gambar));
        }

        if($om_barang_gambar->delete()){
            DB::commit();

            return response()->json(['meta' => ['code' => 200,'message' => 'Berhasil delete varian.']]);
        }else{
            DB::rollBack();

            throw new \DataErrorExceptions('Query error','o_merchant_barang_varians');
        }
    }
}
