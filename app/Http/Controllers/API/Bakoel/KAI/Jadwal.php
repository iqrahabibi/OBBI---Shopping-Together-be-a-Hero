<?php

namespace App\Http\Controllers\API\Bakoel\KAI;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use App\Helper\Form as FM;

class Jadwal extends Controller
{
    public function __invoke(Request $request){
        (new FM)->required($request,[
            'asal','tujuan'
        ]);

        $client = new Client();

        $response   = $client->request('POST',config("app.url")."/kai/jadwal.php",[
            "form_params" => [
                "asal"      => $request->asal,
                "tujuan"    => $request->tujuan
            ]
        ]);

        // dd($response->getBody()->getContents());

        $responses[]  = json_decode($response->getBody()->getContents(),true);

        $data   = array();

        if(!empty($responses))
        {

            foreach($responses as $key => $value)
            {
                if($value['errCode'] == 0){
                
                    $success['code'] = 200;

                    $data['access_token']   = array('token'=>$request->header('Authorization'));
                    
                    $data['schedule']   = $value['schedule'];
                    return response()->json(['meta' => $success,'data' => $data]);
                }
            }
        }else{
            $success['code'] = $value['responseCode'];
            $success['message']= $value['message'];

            return response()->json(['meta' => $success]);
        }
    }
}
