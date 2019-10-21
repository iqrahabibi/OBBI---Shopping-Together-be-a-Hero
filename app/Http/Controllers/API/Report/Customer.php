<?php

namespace App\Http\Controllers\API\Report;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Detail;

use DB;

class Customer extends Controller
{
    public function index(Request $request){
        $customer   = new User();
        
        if($request->input('status'))
        {
            $customer   = $customer->where([
                ['is_verified','=',1],
                ['user_type','=',2]
            ]);
        }else{
            $customer   = $customer->where('user_type',2);
        }

        if($request->input('startdate') && $request->input('finishdate')){
            $customer = $customer->whereDate('created_at','>=', $request->startdate)->whereDate('created_at','<=',$request->finishdate);
        }

        $customer   = $customer->with('detail')->get();

        $data['access_token']   = array('token'=>$request->header('Authorization'));
        $data['custumer']       = $customer;
        
        $success['code']    = 200;
        return response()->json(['meta' => $success,'data' => $data]);
    }
}
