<?php

namespace App\Http\Controllers\API\Bakoel\PLN\Pasca;

use App\Helper\Data;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use App\Helper\Form as FM;

use DB;
use Validator;

class Inquiry extends Controller {
    //    public function __invoke (Request $request) {
    //        (new FM)->required($request, [
    //            'idPelanggan'
    //        ]);
    //
    //
    //        $idPelanggan = $request->idPelanggan;
    //        $idPelanggan2 = $request->idPelanggan2;
    //
    //        $client = new Client();
    //
    //        $response = $client->request('GET', config('app.url') . "/pln/tagihan/postpaid.php?idPel=" . $idPelanggan);
    //        $responses[] = json_decode($response->getBody()->getContents(), true);
    //
    //        $json = [];
    //        $data = [];
    //
    //        if ( empty($response) ) {
    //            $success['code'] = 200;
    //            $success['message'] = "kosong";
    //        }
    //
    //        foreach ( $responses as $key => $value ) {
    //            if ( $value['responseCode'] == 00 ) {
    //                $success['code'] = 200;
    //                $success['message'] = $value['message'];
    //
    //                $data['access_token'] = [ 'token' => $request->header('Authorization') ];
    //
    //                $jml = count($value['detilTagihan']);
    //
    //                for ( $i = 0; $i < $jml; $i++ ) {
    //                    /*$json[$i]["periode"] = $value['detilTagihan'][$i]['periode'];
    //                    $json[$i]["nilaiTagihan"] = $value['detilTagihan'][$i]['nilaiTagihan'];
    //                    $json[$i]["denda"] = $value['detilTagihan'][$i]['denda'];
    //                    $json[$i]["admin"] = $value['detilTagihan'][$i]['admin'];
    //                    $json[$i]["total"] = $value['detilTagihan'][$i]['total'];*/
    //
    //                    $json[] = [
    //                        'periode'      => $value['detilTagihan'][$i]['periode'],
    //                        'nilaiTagihan' => $value['detilTagihan'][$i]['nilaiTagihan'],
    //                        'denda'        => $value['detilTagihan'][$i]['nilaiTagihan'],
    //                        'admin'        => $value['detilTagihan'][$i]['admin'],
    //                        'total'        => $value['detilTagihan'][$i]['total']
    //                    ];
    //
    //                    $data['postpaid'] = [
    //                        "subcriberId"        => $value['subscriberID'],
    //                        "nama"               => $value['nama'], "tarif" => $value['tarif'], "daya" => $value['daya'],
    //                        "lembarTagihanTotal" => $value['lembarTagihanTotal'],
    //                        "lembarTagihan"      => $value['lembarTagihan'],
    //                        "detilTagihan"       => $json,
    //                        "totalTagihan"       => $value['totalTagihan'],
    //                        "productCode"        => $value['productCode'],
    //                        "refID"              => $value['refID']
    //                    ];
    //                }
    //
    //                return response()->json([ 'meta' => $success, 'data' => $data ]);
    //            } else {
    //                $success['code'] = $value['responseCode'];
    //                $success['message'] = $value['message'];
    //
    //                return response()->json([ 'meta' => $success ]);
    //            }
    //        }
    //    }

    public function __invoke (Request $request) {
        $check = Validator::make($request->input(), [
            'idPelanggan' => 'required',
            'user_id'     => 'required',
        ], [
            'idPelanggan.required' => 'Id pelanggan harus di isi.',
            'user_id.required'     => 'User id harus di isi.'
        ]);

        if ( !$check->passes() ) {
            return [
                'meta' => [
                    'code'    => 500,
                    'message' => $check->errors()->all()[0]
                ]
            ];
        }

        $id = $request->input('idPelanggan');
        $rsp = (new Client())
            ->request('GET', config('app.url') . "/pln/tagihan/postpaid.php?idPel={$id}")
            ->getBody()->getContents();

        $response = json_decode($rsp, true);
        if ( json_last_error() != JSON_ERROR_NONE ) {
            return [
                'meta' => [
                    'code'    => 500,
                    'message' => 'Tidak dapat mengambil data dari server.'
                ]
            ];
        }

        if ( empty($response) ) {
            return [
                'meta' => [
                    'code'    => 400,
                    'message' => 'Tidak ada respon yang dikirimkan oleh server.'
                ]
            ];
        }

        $response = (array) $response;

        if ( $response['responseCode'] != "00" ) {
            return [
                'code'    => $response['responseCode'],
                'message' => $response['message']
            ];
        }

        $tagihan = [];
        for ( $i = 0; $i < count($response['detilTagihan']); $i++ ) {
            $tagihan[] = [
                'periode'      => $response['detilTagihan'][$i]['periode'],
                'nilaiTagihan' => $response['detilTagihan'][$i]['nilaiTagihan'],
                'denda'        => $response['detilTagihan'][$i]['nilaiTagihan'],
                'admin'        => $response['detilTagihan'][$i]['admin'],
                'total'        => $response['detilTagihan'][$i]['total']
            ];
        }

        return (new Data())->respond([
            'purchas_post' => [
                "subcriberId"        => $response['subscriberID'],
                "nama"               => $response['nama'],
                "tarif"              => $response['tarif'],
                "daya"               => $response['daya'],
                "lembarTagihanTotal" => $response['lembarTagihanTotal'],
                "lembarTagihan"      => $response['lembarTagihan'],
                "totalTagihan"       => $response['totalTagihan'],
                "productCode"        => $response['productCode'],
                "refID"              => $response['refID'],
                "detilTagihan"       => $tagihan
            ]
        ]);
    }
}
