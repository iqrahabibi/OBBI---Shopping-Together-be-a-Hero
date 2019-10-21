<?php

namespace App\Http\Controllers\API\Bakoel\KAI;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use App\Helper\Form as FM;

class Booking extends Controller
{
    public function __invoke(Request $request){
        (new FM)->required($request,[
            'adult',
            'kode_gerbong',
            'no_gerbong',
            'kursi',
            'nama_infant',
            'birth_infant',
            'dewasa',
            'asal',
            'tujuan',
            'tanggal',
            'subclass',
            'trainNumber'
        ]);
        
        $adult = json_decode($request->adult,true);
        
        $data = array();
        
        $i = 0;
        foreach($adult['adult'] as $key => $value){
            $data[$i]['adult_name'] = $value['adult_name'];
            $data[$i]['adult_id'] = $value['adult_id'];
            $data[$i]['adult_date_of_birth'] = $value['adult_birth'];
            $data[$i]['adult_phone'] = $value['adult_phone'];

            $i++;
        }

        $client = new Client();

        $response   = $client->post(config("app.url")."/kai/booking.php",[
            "form_params" => [
                "adult"         => $data,
                "kode_gerbong"  => $request->kode_gerbonga,
                "no_gerbong"    => $request->no_gerbong,
                "kursi"         => $request->kursi,
                "nama_infant"   => $request->nama_infant,
                "birth_infant"  => $request->birth_infant,
                "dewasa"        => $request->dewasa,
                "bayi"          => $request->bayi,
                "tanggal"       => $request->tanggal,
                "asal"          => $request->asal,
                "tujuan"        => $request->tujuan,
                "trainNumber"   => $request->trainNumber,
                "subclass"      => $request->subclass
            ]
        ]);

        $responses[]  = json_decode($response->getBody()->getContents(),true);
        $json   = array();

        if(!empty($responses))
        {

            foreach($responses as $key => $value)
            {
                
                    $success['code'] = 200;

                    $json['access_token']   = array('token'=>$request->header('Authorization'));
                    
                    $json['booking']   = $value;
                    
                    // $value['seat_map'][0][0]
                    //                     ." - kode gerbong = ".$value['seat_map'][0][1]
                    //                     ." - Kursi = ".$value['seat_map'][0][2][2][2].$value['seat_map'][0][2][3][4];
                    return response()->json(['meta' => $success,'data' => $json]);
            }
        }else{
            $success['code'] = $value['responseCode'];
            $success['message']= $value['message'];

            return response()->json(['meta' => $success]);
        }

    }
}
