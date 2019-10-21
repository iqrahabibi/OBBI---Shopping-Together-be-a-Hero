<?php

namespace App\Http\Controllers\API\Report;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Detail;
use App\ReferalOpf;

use DB;

class Referal extends Controller
{
    public function __invoke(Request $request)
    {
        $opf = new ReferalOpf();

        if(!empty($request->input('startdate')) && !empty($request->input('finishdate'))){
            $opf = $opf->whereDate('created_at','>=', $request->startdate)
            ->whereDate('created_at','<=', $request->finishdate);
        }

        $opf = $opf->with('opf')->get();

        $data['access_token']   = array('token'=>$request->header('Authorization'));
        $data['opf']            = $opf;
        
        $success['code']    = 200;
        return response()->json(['meta' => $success,'data' => $data]);
    }
}