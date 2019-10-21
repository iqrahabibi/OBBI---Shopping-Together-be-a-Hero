<?php

namespace App\Http\Controllers\API\Toko\OMercant\Barang\Gambar;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helper\Form as FM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\OBBI\obbiHelper;

use App\Model\OMerchantBarangVarian;
use App\Model\OMerchantAdmin;

use DB;
use File;

class Update extends Controller
{
    public function __invoke(Request $request){
        (new FM)->required($request,[
            'barang_id','gambar_id'
        ]);

        DB::beginTransaction();

        $om_barang_gambar   = OMerchantBarangGambar::findOrFail($request->gambar_id);
        $om_barang_gambar->barang_id        = $request->barang_id;

        if($request->hasFile('barang_gambar')){
            $image              = $request->file('barang_gambar');

            if(!in_array($image->getMimeType(),['image/jpeg', 'image/jpg', 'image/png']))
            {
                throw new \SecurityExceptions('Format foto tidak sesuai.');
            }

            $extension          = $image->getClientOriginalExtension();

            $filename           = str_random(40).$extension;

            $destinationPath    = storage_path().DIRECTORY_SEPARATOR.'app/public'.DIRECTORY_SEPARATOR.'omerchant/barang/gambar/';

            $image->move($destinationPath,$filename);

            File::delete(obbiHelper::storage($om_barang_gambar->barang_gambar));

            $om_barang_gambar->barang_gambar = obbiHelper::storage('/omerchant/barang/gambar/'.$filename);
        }

        if($om_barang_gambar->update()){
            DB::commit();

            return (new \Data)->respond([
                'access_token'  => array('token'=>$request->header('Authorization')),
                'om_barang_gambar' => $om_barang_gambar,
            ]);
        }else{
            DB::rollBack();

            throw new \DataErrorExceptions('Query error','o_merchant_barang_varians');
        }
    }
}
