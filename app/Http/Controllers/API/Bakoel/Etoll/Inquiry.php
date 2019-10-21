<?php

namespace App\Http\Controllers\API\Bakoel\Etoll;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use App\Helper\Form as FM;

class Inquiry extends Controller
{
    public function __invoke(Request $request){
        $client = new Client();

        $response   = $client->request('GET',config("app.url")."/etoll/index.php");

        $responses[]  = json_decode($response->getBody()->getContents(),true);

        $data   = array();

        if(!empty($responses))
        {

            foreach($responses as $key => $value)
            {
                
                    $success['code'] = 200;

                    $data['access_token']   = array('token'=>$request->header('Authorization'));
                    
                    $data['etoll']   = $value['productList'];
                    return response()->json(['meta' => $success,'data' => $data]);

                
            }
        }else{
            $success['code'] = $value['responseCode'];
            $success['message']= $value['message'];

            return response()->json(['meta' => $success]);
        }
    }
}
