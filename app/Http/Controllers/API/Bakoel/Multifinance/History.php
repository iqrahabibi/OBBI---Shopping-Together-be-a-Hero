<?php

namespace App\Http\Controllers\API\Bakoel\Multifinance;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helper\Form as FM;

use App\Model\DigiPay;

class History extends Controller
{
    public function __invoke(Request $request){
        (new FM)->required($request,[
            'user_id','code'
        ]);

        $history = DigiPay::with('user')->where([
            ['user_id','=',$request->user_id],
            ['notes','like','%'.$request->code.'%']
        ])->get();

        return (new \Data)->respond([
            'access_token'  => $request->header('Authorization'),
            'history_multifinance'   => $history
        ]);
    }
}
