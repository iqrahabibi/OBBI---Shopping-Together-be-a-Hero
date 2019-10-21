<?php

namespace App\Http\Controllers\API\Revoke;

use Illuminate\Auth\AuthenticationException;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\RevokeModel;
use DB;

class Update extends Controller
{
    public function update(Request $request)
    {
        
        DB::beginTransaction();

        try{
            $data = RevokeModel::where('user_id',$request->id_user)->update(['revoked' => 1]);
            
            DB::commit();

            $success['code']= 200;
            $success['message']="Berhasil update revoke.";
            $input['access_token']  = array('token' => $request->header('Authorization'));
            
            return response()->json(['meta' => $success,'data' =>$input]);

        }
        catch(QueryException $e)
        {
            DB::rollBack();

            $success['code']= 400;
            $success['message']= $e->getMessage();
            
            return response()->json(['meta' => $success]);
        }
    }
}