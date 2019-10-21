<?php

namespace App\Http\Controllers\API\Bakoel\Pulsa;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use App\Helper\Form as FM;

use DB;

class Cek_trxid extends Controller
{
    public function __invoke(Request $request){
        (new FM)->required($request,[
            'msisdn','trxID'
        ]);

        $client = new Client();

        $response   = $client->request('POST',config('app.url')."/pulsa/cekTrx.php",[
            'form_params' => [
                'msisdn'=> $request->msisdn,
                'trxID' => $request->trxID
            ]
        ]);

        $responses  = json_decode($response->getBody()->getContents(),true);

        if($responses['meta']['code'] == 200)
        {
            $success['code']    = 200;

            return response()->json(['meta' => $success,'cektrxID' => $responses['cektrxID']]);
        }else{
            $success['code']    = 400;
            $success['message'] = $responses['cektrxID'];
            return response(['meta' => $success]);
        }
    }
}
