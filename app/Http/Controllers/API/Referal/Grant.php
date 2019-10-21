<?php

namespace App\Http\Controllers\API\Referal;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Referal;

use DB;

class Grant extends Controller
{
    public function index(Request $request)
    {

        if(empty($request->user_id))
        {
            $success['code']    = 400;
            $success['message'] = "Parameter user id tidak boleh kosong.";

            return response()->json(['meta'=> $success]);
        }

        if(empty($request->code_referal))
        {
            $success['code']    = 400;
            $success['message'] = "Parameter code referal tidak boleh kosong.";

            return response()->json(['meta'=> $success]);
        }

        $user   = User::where('id',$request->user_id)->first();
        $ref    = Referal::with('user')->where('code_referal',$request->code_referal)->first();

        if($user->user_type != $ref->user_type)
        {
            $success['code']    = 400;
            $success['message'] = "Maaf, code referal tidak sesuai.";

            return response()->json(['meta'=> $success]);
        }

        if($ref->is_active == 1)
        {
            $success['code']    = 400;
            $success['message'] = "Maaf, code referal tidak dapat digunakan.";

            return response()->json(['meta'=> $success]);
        }

        if(empty($ref))
        {
            $success['code']    = 400;
            $success['message'] = "Maaf, code referal tidak ditemukan.";

            return response()->json(['meta'=> $success]);
        }

        DB::beginTransaction();

        try{
            $result = Referal::where('code_referal',$request->code_referal)
            ->update(['is_active' => 1]);

            $user_ = User::where('id',$request->user_id)->update(['user_type' => 1]);

            DB::commit();
            $success['code']    = 200;
            $success['message'] = "Berhasil menggunakan code referal.";

            return response()->json(['meta' => $success]);
        }catch(QueryException $e)
        {
            DB::rollBack();
            $success['code']    = 400;
            $success['message'] = $e->getMessage();

            return response()->json(['meta'=> $success]);
        }
    }
}
