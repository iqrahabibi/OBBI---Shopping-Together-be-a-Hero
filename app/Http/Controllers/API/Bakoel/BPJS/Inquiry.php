<?php

namespace App\Http\Controllers\API\Bakoel\BPJS;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use App\Helper\Form as FM;

class Inquiry extends Controller
{
    public function __invoke(Request $request)
    {

        (new FM)->required($request,[
            'idpel','period'
        ]);

        $notelp = "";
        if($request->input('notelp'))
        {
            $notelp = $request->notelp;
        }

        $client = new Client();

        $response   = $client->request('GET',config("app.url")."/bpjskes/index.php?idpel=".$request->idpel."&period=".$request->period."&notelp=".$notelp);

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
                    
                    $data['bpjs']   = array("nama" => $value['nama'],
                    'cabang' => $value['namaCabang'],'jumlahPeriode' => $value['jumlahPeriode'],
                    'jumlahPeserta' => $value['jumlahPeserta'],'detailPeserta' => $value['detailPeserta'],
                    'tagihan' => $value['tagihan'],'admin' => $value['admin'],'total'=> $value['total'],
                    'customerData' => $value['customerData'],'productCode' => $value['productCode'],'refID' => $value['refID']);

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
