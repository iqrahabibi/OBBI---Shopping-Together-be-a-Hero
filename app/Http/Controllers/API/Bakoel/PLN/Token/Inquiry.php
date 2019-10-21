<?php

namespace App\Http\Controllers\API\Bakoel\PLN\Token;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use App\Helper\Form as FM;

use DB;

class Inquiry extends Controller
{
    public function __invoke(Request $request){
        (new FM)->required($request,[
            'idPelanggan'
        ]);

        $idPelanggan    = $request->idPelanggan;
        $idPelanggan2   = $request->idPelanggan2;
        $miscData       = $request->miscData;

        $client = new Client();

        $response   = $client->request('GET',config('app.url')."/pln/token/prepaid.php?idPel=".$idPelanggan."&miscData=");

        $responses[]  = json_decode($response->getBody()->getContents(),true);

        $json   = array();
        $data   = [];

        foreach($responses as $key => $value)
        {
            if($value['responseCode'] == 00)
            {
                $success['code'] = 200;
                $success['message']= $value['message'];

                $data['access_token']   = array('token'=>$request->header('Authorization'));
                
                if(empty($value['powerPurchaseUnsold']))
                {
                    $json['powerPurchaseUnsold'] = [];
                }else{
                    $json['powerPurchaseUnsold'] = $value['powerPurchaseUnsold'];
                }

                $jml = count($value['data']);

                $data['prepaid']   = array('data' => $value['data'],
                "powerPurchaseDenom" => $value['powerPurchaseDenom'],
                "powerPurchaseUnsold" => $json['powerPurchaseUnsold'],
                "refId" => $value['refID']);

                return response()->json(['meta' => $success,'data' => $data]);
            }else{
                $success['code'] = $value['responseCode'];
                $success['message']= $value['message'];
    
                return response()->json(['meta' => $success]);
            }
        }
    }
}
