<?php

namespace App\Http\Controllers\API\Bakoel\KAI;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use App\Helper\Form as FM;

class Kursi extends Controller
{
    public function __invoke(Request $request){
        (new FM)->required($request,[
            'trainNumber','asal','tujuan','tanggal'
        ]);

        $client = new Client();

        $response   = $client->post(config("app.url")."/kai/kursi.php",[
            "form_params" => [
                "trainNumber"   => $request->trainNumber,
                "tanggal"       => $request->tanggal,
                "asal"          => $request->asal,
                "tujuan"        => $request->tujuan,
            ]
        ]);

        $responses[]  = json_decode($response->getBody()->getContents(),true);
        $data   = array();

        if(!empty($responses))
        {

            foreach($responses as $key => $value)
            {
                
                    $success['code'] = 200;

                    $data['access_token']   = array('token'=>$request->header('Authorization'));
                    
                    $data['seatmap']   = $value['seat_map'][0][2][3][4];
                    // "kode gerbong = ".$value['seat_map'][0][0]
                    // ." - no gerbong = ".$value['seat_map'][0][1]
                    // ." - Kursi = ".$value['seat_map'][0][2][2][2].$value['seat_map'][0][2][3][4];
                    // $value['seat_map'];
                    
                    return response()->json(['meta' => $success,'data' => $data]);
            }
        }else{
            $success['code'] = $value['responseCode'];
            $success['message']= $value['message'];

            return response()->json(['meta' => $success]);
        }
    }
}
