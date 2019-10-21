<?php

namespace App\Http\Controllers\API\PIN;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\PIN;
use DB;

class Create extends Controller
{
    public function index(Request $request)
    {
        $data   = $request->all();

        if(empty($data['user_id']))
        {
            $success['code']    = 404;
            $success['message'] = "User id tidak boleh kosong.";

            return response()->json(['meta'=> $success]);
        }

        if(empty($data['pin']))
        {
            $success['code']    = 404;
            $success['message'] = "PIN tidak boleh kosong.";

            return response()->json(['meta'=> $success]);
        }

        if(strlen($data['pin']) != 6 )
        {
            $success['code']    = 404;
            $success['message'] = "PIN harus 6 digit.";

            return response()->json(['meta'=> $success]);
        }

        DB::beginTransaction();
        try
        {
            $chek   = PIN::where('user_id',$data['user_id'])->get()->count();

            if($chek > 0)
            {
                $update = PIN::where('id',$data['user_id'])->update(['pin' => $data['pin']]);

                DB::commit();
                $success['code']    = 200;
                $success['message'] = "sukses";

                $input['access_token']  = array('token' => $request->header('Authorization'));
            
                $input['pin']   = $update;
                
                return response()->json(['meta'=> $success,'data' => $input]);
            }else{
                if(PIN::where('pin',$data['pin'])->first() != null)
                {
                    $success['code']    = 404;
                    $success['message'] = "Nomor PIN sudah ada, silahkan ganti dengan nomor lain.";

                    return response()->json(['meta'=> $success]);
                }
                $result = new PIN();
                $result->user_id    = $data['user_id'];
                $result->pin        = $data['pin'];

                $result->save();

                DB::commit();
                $success['code']    = 200;
                $success['message'] = "sukses";

                $input['access_token']  = array('token' => $request->header('Authorization'));
            
                $input['pin']   = $result;
                
                return response()->json(['meta'=> $success,'data' => $input]);
            }
        }
        catch(QueryException $e)
        {
            DB::rollBack();
            $success['code']    = 400;
            $success['message'] = $e->getMessage();

            return response()->json(['meta'=> $success]);
        }
    }
}
