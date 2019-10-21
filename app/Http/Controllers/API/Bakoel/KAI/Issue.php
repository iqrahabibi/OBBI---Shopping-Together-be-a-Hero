<?php

namespace App\Http\Controllers\API\Bakoel\KAI;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use App\Helper\Form as FM;

class Issue extends Controller
{
    public function __invoke(Request $request){
        (new FM)->required($request,[
            'bookingCode',
            'totalPrice'
        ]);

        $client = new Client();

        $response   = $client->post(config("app.url")."/kai/issue.php",[
            "form_params" => [
                "bookingCode"   => $request->bookingCode,
                "totalPrice"    => $request->totalPrice
            ]
        ]);

        $responses[]  = json_decode($response->getBody()->getContents(),true);
        $data   = array();

        if(!empty($responses)){

            foreach($responses as $key => $value){
                $success['code']    = 200;

                $data['issue']      = $value;

                return response()->json(['meta' => $success,'data' => $data]);
            }
        }else{
            $success['code'] = 404;
            $success['message'] = "Data not found.";

            return response()->json(['meta' => $success]);
        }
    }
}
