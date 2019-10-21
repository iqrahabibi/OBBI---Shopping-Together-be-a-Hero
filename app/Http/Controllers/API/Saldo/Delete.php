<?php

namespace App\Http\Controllers\API\Saldo;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helper\Form as FM;

use App\Model\DigiPay;

use DB;

class Delete extends Controller
{
    public function __invoke(Request $request){
        // (new FM)->required($request,[
        //     'balance_id'
        // ]);
        
        // DB::beginTransaction();
        
        // $delete = DigiPay::where('id',$request->balance_id)->delete();

        // DB::commit();

        // return (new \Data)->respond([
        //     'access_token'  => $request->header('Authorization')
        // ]);
    }
}
