<?php

namespace App\Http\Controllers\API\Bakoel\PDAM;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use App\Helper\Form as FM;

class Inquiry extends Controller
{
    public function __invoke(Request $request)
    {
        (new FM)->required($request,[
            'idpel','product_code'
        ]);

        $client = new Client();

        $response   = $client->request('GET',config("app.url")."/pdam/index.php?idpel=".$request->idpel."&product_code=".$request->product_code);

        $responses[]  = json_decode($response->getBody()->getContents(),true);

        $data   = array();

        if(!empty($responses))
        {

            foreach($responses as $key => $value)
            {
                if($value['responseCode'] == 00)
                {
                    $success['code'] = 200;
                    $success['message']= $value['message'];

                    $data['access_token']   = array('token'=>$request->header('Authorization'));
                    
                    $data['pdam']   = $value;
                    return response()->json(['meta' => $success,'data' => $data]);

                }else{
                    $success['code'] = $value['responseCode'];
                    $success['message']= $value['message'];

                    return response()->json(['meta' => $success]);
                }
            }
        }else{
            $success['code'] = $value['responseCode'];
            $success['message']= $value['message'];

            return response()->json(['meta' => $success]);
        }
    }
}
