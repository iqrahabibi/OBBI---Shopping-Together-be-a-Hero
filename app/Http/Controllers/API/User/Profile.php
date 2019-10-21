<?php

namespace App\Http\Controllers\API\User;

use App\Helper\ObbiAssets;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\OBBI\obbiHelper;

use App\Model\User;
use App\Model\DetailUser;
use App\Model\Agama;

use DB;
use File;

class Profile extends Controller {
    public function __invoke (Request $request) {

        DB::beginTransaction();

        $data_user = User::with('detail', 'saldo')->where('id', $request->user_id)->first();

        $data_user->fullname = $request->fullname;
        $data_user->phone = $request->phone;

        $data_user->save();

        $cek_data = DetailUser::where([
            [ 'user_id', '=', $request->user_id ],
            [ 'valid', '=', 1 ]
        ])->first();

        $kelurahan = "";
        $alamat = "";
        $phone = "";
        $agama = "";

        if ( !empty($cek_data) ) {
            $cek_data->valid = 0;

            if ( !empty($request->kelurahan) ) {
                $kelurahan = $request->kelurahan;
            } else {
                $kelurahan = $cek_data->kelurahan_id;
            }

            if ( !empty($request->alamat) ) {
                $alamat = $request->alamat;
            } else {
                $alamat = $cek_data->alamat;
            }

            if ( !empty($request->phone) ) {
                $phone = $request->phone;
            } else {
                $phone = $data_user->phone;
            }

            if ( !empty($request->agama) ) {
                $agama = $request->agama;
            } else {
                $agama = $cek_data->agama;

            }
            $cek_data->agama_id = $agama;
            $cek_data->phone = $phone;
            $cek_data->save();
        }

        // $detail_user = new DetailUser();
        // $detail_user->user_id   = $request->user_id;
        // $detail_user->agama_id  = $request->agama;
        // $detail_user->kelurahan_id  = $request->kelurahan;
        // $detail_user->alamat    = $request->alamat;
        // $detail_user->valid     = 1;
        // $detail_user->phone     = $request->phone;

        // $detail_user->save();

        $detail_user = DetailUser::updateOrCreate([
            'user_id' => $request->user_id
        ], [
            'agama_id' => $request->agama,
            'phone'    => $request->phone,
            'valid'    => 1
        ]);

        DB::commit();
        $data_user->image = ObbiAssets::get_asset(ObbiAssets::USER_PROFILE, $data_user->image);
        return (new \Data)->respond([
            'detail_user' => $data_user
        ]);
    }
}
