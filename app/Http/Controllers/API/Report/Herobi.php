<?php

namespace App\Http\Controllers\API\Report;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\User;
use App\DetalUser;
use App\Herobi as HB;

use DB;

class Herobi extends Controller
{
    public function __invoke(Request $request){
        $herobi = HB::where('valid',1);

        if(!empty($request->input('startdate')) && !empty($request->input('finishdate'))){
            $herobi = $herobi->whereDate('created_at','>=',$request->startdate)
            ->whereDate('created_at','<=', $request->finishdate);
        }

        $herobi = $herobi->with('user')->get();

        $data['access_token']   = array('token'=>$request->header('Authorization'));
        $data['herobi']       = $herobi;
        
        $success['code']    = 200;
        return response()->json(['meta' => $success,'data' => $data]);
    }
}
