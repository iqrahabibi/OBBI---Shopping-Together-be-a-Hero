<?php

namespace App\Http\Controllers\API\Paid;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helper\Form as FM;

use App\Model\DigiPay;
use App\Model\User;

use Auth;
use DB;

class Inquiry extends Controller
{
    public function __invoke(Request $request){
        (new FM)->required($request,[
            'invoice'
        ]);

        $digi_pay   = DigiPay::with('user')->where([
            ['invoice','=',$request->invoice],
            ['valid','=',0]
        ])->first();

        if(empty($digi_pay)){
            throw new \DataNotFoundExceptions('Data tidak ditemukan.','digipays');
        }

        return (new \Data)->respond([
            'access_token'  => array('token'=>$request->header('Authorization')),
            'inquiry' => $digi_pay
        ]);
    }
}
