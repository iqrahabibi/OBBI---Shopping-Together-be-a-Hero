<?php

namespace App\Http\Controllers\API\Bakoel\Pulsa\Voucher;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helper\Form as FM;

use App\Model\DigiPay;

use DB;

class History extends Controller
{
    public function __invoke(Request $request){
        (new FM)->required($request,[
            'user_id'
        ]);

        $result = DigiPay::where([
            ['user_id','=',$request->user_id],
            ['notes','like','%voucher pulsa%']
        ])->orderBy('created_at','desc')->get();

        if(empty($result))
        {
            throw new \DataNotFoundExceptions('Tidak ada transaksi.','digi_pays');
        }else{
            $data['history']    = $result;

            return (new \Data)->respond([
                'access_token'  => array('token'=>$request->header('Authorization')),
                'data' => $data
            ]);
        }
    }
}
