<?php

namespace App\Http\Controllers\API\Bakoel\PLN\Token;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\DigiPay;

use DB;

class History extends Controller
{
    public function __invoke(Request $request){

        $result = DigiPay::where([
            ['user_id','=',$request->user_id],
            ['notes','like','%'.$request->code.'%']
        ])->orderBy('created_at','desc')->get();

        if(empty($result))
        {
            throw new \DataNotFoundExceptions('Data tidak ditemukan.','digi_pays');
        }else{

            $data['history'] = $result;

            return (new \Data)->respond([
                'access_token'  => array('token'=>$request->header('Authorization')),
                'data' => $data
            ]);
        }
    }
}
