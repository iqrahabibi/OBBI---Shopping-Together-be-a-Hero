<?php

namespace App\Http\Controllers\API\Referal;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Referal;
use DB;

class Create extends Controller
{
    public function index(Request $request)
    {
        if(empty($request->user_id))
        {
            $success['code']    = 400;
            $success['message'] = "Parameter user id tidak boleh kosong.";

            return response()->json(['meta'=> $success]);
        }

        $user = User::where('id',$request->user_id)->first();

        if($user->user_type == 2)
        {
            $success['code']    = 400;
            $success['message'] = "Referal hanya dapat digunakan oleh level HEROBI ke atas.";

            return response()->json(['meta'=> $success]);
        }

        DB::beginTransaction();

        $user_type = 2;

        try{
            $code_referal = str_random(20);

            $ref = Referal::where('code_referal',$code_referal)->get()->count();

            if($ref > 0)
            {
                $code_referal = str_random(20);
            }

            if($user->user_type == 1)
            {
                $user_type = 2;
            }else if($user->user_type == 4){
                $user_type = 1;
            }

            $result = new Referal();
            $result->user_id        = $request->user_id;
            $result->code_referal   = strtoupper($code_referal);
            $result->is_active      = 0;
            $result->user_type      = $user_type;

            $result->save();

            DB::commit();
            $success['code']    = 200;
            $success['message'] = "Berhasil membuat code referal.";
            $data['access_token']= array('token'=>$request->header('Authorization'));
            $data['create']     = $result;

            return response()->json(['meta' => $success,'data'=>$data]);

        }catch(QueryException $e)
        {
            DB::rollBack();
            $success['code']    = 400;
            $success['message'] = $e->getMessage();

            return response()->json(['meta'=> $success]);
        }
    }
}
