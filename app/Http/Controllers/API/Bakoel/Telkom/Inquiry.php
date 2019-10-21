<?php

namespace App\Http\Controllers\API\Bakoel\Telkom;

use App\Rules\Bakoel\WhitelistCodeTelkom;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class Inquiry extends Controller {
    /*public function __invoke (Request $request) {
        (new FM)->required($request, [
            'idpel'
        ]);

        $client = new Client();

        $response = $client->post(config("app.url") . "/telkom/inquiry.php", [
            "form_params" => [
                "idpel" => $request->idpel
            ]
        ]);

        $responses[] = json_decode($response->getBody()->getContents(), true);

        $data = [];

        if ( !empty($responses) ) {

            foreach ( $responses as $key => $value ) {
                if ( $value['responseCode'] == 00 ) {
                    $success['code'] = 200;
                    $success['message'] = $value['message'];

                    $data['access_token'] = [ 'token' => $request->header('Authorization') ];

                    $data['telkom'] = [ "data" => $value ];

                    return response()->json([ 'meta' => $success, 'data' => $data ]);

                } else {
                    $success['code'] = $value['responseCode'];
                    $success['message'] = $value['message'];

                    return response()->json([ 'meta' => $success ]);
                }
            }
        } else {
            $success['code'] = $value['responseCode'];
            $success['message'] = $value['message'];

            return response()->json([ 'meta' => $success ]);
        }
    }*/

    public function __invoke (Request $request) {
        $check = Validator::make($request->input(), [
            'id_pelanggan' => [ 'required' ],
            'user_id'      => [ 'required' ],
            'kode_produk'  => [ 'required', new WhitelistCodeTelkom() ]
        ], [
            'id_pelanggan.required' => 'Id pelanggan harus di isi.',
            'user_id.required'      => 'User id harus di isi.',
            'kode_produk.required'  => 'Kode produk harus di isi.'
        ]);

        if ( $check->fails() )
            return [ 'meta' => [ 'code' => 500, 'message' => $check->errors()->all()[0] ] ];

        $client = (new Client())->request('POST', config('app.url') . "/telkom/inquiry.php", [
            "form_params" => [
                'product_id' => ($request->input('kode_produk') == "TSPEEDY") ? "TELKOMSPEEDY" : $request->input('kode_produk'),
                'client_id'  => $request->input('id_pelanggan')
            ]
        ]);

        $json = json_decode($client->getBody()->getContents());
        if ( (json_last_error() != JSON_ERROR_NONE) || empty($json) )
            return [ 'meta' => [ 'code' => 500, 'message' => 'Tidak dapat mengambil data dari server.' ] ];

        if ( $json->responseCode != '00' ) {
            switch ( $json->responseCode ) {
                default :
                    $message = $json->message;
            }

            return [ 'meta' => [ 'code' => 500, 'message' => $message ] ];
        }

        $tagihan = [];
        foreach ( $json->tagihan as $data ) {
            $tagihan[] = [
                'periode'       => $data->periode,
                'nilai_tagihan' => (int) $data->nilaiTagihan,
                'admin'         => (int) $data->admin,
                'total'         => (int) $data->total,
                'fee'           => (int) $data->fee
            ];
        }

        return [
            'meta' => [
                'code'    => 200,
                'message' => ''
            ],
            'data' => [
                'id_pelanggan'   => $json->idpel,
                'ref_id'         => $json->refID,
                'kode_area'      => $json->kodeArea,
                'jumlah_tagihan' => $json->jumlahTagihan,
                'nama_pelanggan' => $json->nama,
                'total_tagihan'  => $json->totalTagihan,
                'tagihan'        => $tagihan
            ]
        ];
    }
}
