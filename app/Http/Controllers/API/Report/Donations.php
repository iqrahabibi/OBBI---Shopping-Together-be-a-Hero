<?php

namespace App\Http\Controllers\API\Report;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Detail;
use App\Donation;

use DB;

class Donations extends Controller
{
    public function __invoke(Request $request)
    {
        $donations = new Donation();

        if($request->input('month') != null){
            $donations = $donations->whereMonth('created_at','=', $request->month);
        }

        $donations = $donations->with('user')->get();

        $data['access_token']   = array('token'=>$request->header('Authorization'));
        $data['donation']            = $donations;
        
        $success['code']    = 200;
        return response()->json(['meta' => $success,'data' => $data]);
    }
}