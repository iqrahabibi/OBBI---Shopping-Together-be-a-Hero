<?php

namespace App\Http\Controllers\API\OPF;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\Opf;
use App\Model\User;
use App\Model\PengaduanOpf;

use Validator;
use DB;

class CreateAduan extends Controller
{
    public function __invoke(Request $request)
    {
        $data   = $request->all();

        if(empty($data))
        {
            $success['code']    = 404;
            $success['message'] = "Tidak ada data yang dikirim.";

            return response()->json(['meta'=> $success]);
        }
        DB::beginTransaction();
        
        try{
            $cek = $this->cekuser($data['id_user']);
            $opf = $this->cekopf($data['opf_id']);

            if($cek == false)
            {
                $success['code']    = 404;
                $success['message'] = "user tidak ada.";

                return response()->json(['meta'=> $success]);
            }

            if($opf == false)
            {
                $success['code']    = 404;
                $success['message'] = "opf tidak ada.";

                return response()->json(['meta'=> $success]);
            }

            $aduan = new PengaduanOpf();
            $aduan->user_id         = $data['id_user'];
            $aduan->opf_id          = $data['opf_id'];
            $aduan->aduan           = $data['aduan'];
            $aduan->valid           = 0;

            $aduan->save();

            DB::commit();
            $success['code']    = 200;
            $success['message'] = "Berhasil menyimpan aduan opf.";
            $data['access_token']= array('token'=>$request->header('Authorization'));
            $data['create']     = $aduan;

            return response()->json(['meta' => $success,'data'=>$data]);
        }catch(QueryException $e){
            
            DB::rollback();
            $success['code']    = 400;
            $success['message'] = $e->getMessage();

            return response()->json(['meta'=> $success]);
        }
    }

    public function cekuser($user)
    {
        $cek    = user::where('id',$user)->first();

        if(!empty($cek))
        {
            return true;
        }else{
            return false;
        }
    }

    public function cekopf($opf)
    {
        $cek    = Opf::where('id',$opf)->first();

        if(!empty($cek))
        {
            return true;
        }else{
            return false;
        }
    }
}