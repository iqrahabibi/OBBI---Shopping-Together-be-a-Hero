<?php

namespace App\Http\Controllers\API\Bakoel\KAI;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use App\Helper\Form as FM;

class Subclass extends Controller
{
    public function __invoke(Request $request){
        (new FM)->required($request,[
            'trainNumber','asal','tujuan','tanggal','subclass'
        ]);

        $client = new Client();

        $response   = $client->post(config("app.url")."/kai/subclass.php",[
            "form_params" => [
                "trainNumber"   => $request->trainNumber,
                "tanggal"       => $request->tanggal,
                "asal"          => $request->asal,
                "tujuan"        => $request->tujuan,
                "subclass"      => $request->subclass
            ]
        ]);

        $responses[]  = json_decode($response->getBody()->getContents(),true);

        var_dump($responses);
        exit();

        $data   = array();

        if(!empty($responses))
        {

            foreach($responses as $key => $value)
            {
                
                    $success['code'] = 200;

                    $data['access_token']   = array('token'=>$request->header('Authorization'));
                    
                    $data['schedule']   = $value['schedule'];
                    return response()->json(['meta' => $success,'data' => $data]);
            }
        }else{
            $success['code'] = $value['responseCode'];
            $success['message']= $value['message'];

            return response()->json(['meta' => $success]);
        }
    }
}
