<?php

namespace App\Http\Controllers\API\Bakoel\KAI;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;

class Stasiun extends Controller
{
    public function __invoke(Request $request){
        $client = new Client();

        $response   = $client->request('GET',config("app.url")."/kai/stasiun.php");

        $responses[]  = json_decode($response->getBody()->getContents(),true);

        $data   = array();

        if(!empty($responses))
        {

            foreach($responses as $key => $value)
            {
                
                    $success['code'] = 200;

                    $data['access_token']   = array('token'=>$request->header('Authorization'));
                    
                    $data['stasiun']   = $value;
                    return response()->json(['meta' => $success,'data' => $data]);

                
            }
        }else{
            $success['code'] = $value['responseCode'];
            $success['message']= $value['message'];

            return response()->json(['meta' => $success]);
        }
    }
}
