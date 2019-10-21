<?php

namespace App\Http\Controllers\API\Alamat;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helper\Form as FM;

use App\Model\User;
use App\Model\UserAlamat;

use DB;

class Create extends Controller
{
    public function __invoke(Request $request)
    {
        (new FM)->required($request,[
            'alamat','user_id','kode_kecamatan','type','phone','kelurahan_id'
        ]);

        DB::beginTransaction();
            
        $alamat = new UserAlamat();
        $alamat->user_id        = $request->user_id;
        $alamat->alamat         = $request->alamat;
        $alamat->kode_kecamatan = $request->kode_kecamatan;
        $alamat->type           = $request->type;
        $alamat->kelurahan_id   = $request->kelurahan_id;

        if($request->input('phone')){
            $alamat->phone  = $request->phone;
        }

        if($alamat->save()){
            DB::commit();

            $success['code']    = 200;
            $success['message'] = "Berhasil menyimpan alamat user.";

            $data['access_token']= array('token'=>$request->header('Authorization'));
            $data['create']     = $alamat;

            return response()->json(['meta' => $success,'data'=>$data]);
        }else{
            DB::rollBack();

            throw new \SecurityExceptions('terjadi kesalahan pada query.');
        }
    }
}