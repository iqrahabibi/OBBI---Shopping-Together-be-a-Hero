<?php

namespace App\Http\Controllers\API\Alamat;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helper\Form as FM;

use App\Model\UserAlamat;
use App\Model\User;

use DB;

class Delete extends Controller
{
    public function __invoke(Request $request)
    {
        (new FM)->required($request,[
            'id'
        ]);

        DB::beginTransaction();

        try
        {
            $alamat   = UserAlamat::where('id',$request->id)->delete();
            DB::commit();

            $success['code']    = 200;
            $success['message'] = "Berhasil menghapus alamat pengiriman.";

            return response()->json(['meta'=> $success]);
        }catch(QueryException $e)
        {
            DB::rollBack();
            $success['code']    = 400;
            $success['message'] = $e->getMessage();

            return response()->json(['meta'=> $success]);
        }
    }
}