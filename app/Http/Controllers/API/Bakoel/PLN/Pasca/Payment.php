<?php

namespace App\Http\Controllers\API\Bakoel\PLN\Pasca;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Mail;
use App\Helper\Form as FM;
use App\Model\DigiPay;
use App\Model\Saldo;
use App\Model\Finance;
use App\Model\User;
use DB;
use Validator;

class Payment extends Controller {
    public function __invoke (Request $request) {
        $check = Validator::make($request->input(), [
            'refID'        => 'required',
            'totalTagihan' => 'required',
            'user_id'      => 'required',
            'code'         => 'required|exists:finances,kode'
        ], [
            'refID.required'        => 'Id referensi harus di isi.',
            'totalTagihan.required' => 'Total tagihan harus di isi.',
            'user_id.required'      => 'User id harus di isi.',
            'code.exists'           => 'Kode tersebut tidak terdaftar dalam database.'
        ]);

        if ( !$check->passes() ) {
            return [
                'meta' => [
                    'code'    => 500,
                    'message' => $check->errors()->all()[0]
                ]
            ];
        }

        $totalTagihan = $request->totalTagihan;
        $refID = $request->refID;
        $userid = $request->user_id;
        $code = $request->code;

        $saldo = 0;

        $balance = Saldo::where('user_id', $userid)->first();

        if ( $balance->saldo < $totalTagihan ) {
            $success['code'] = 300;
            $success['message'] = "Saldo anda tidak mencukupi, silahkan lakukan pengisian ulang.";

            return response()->json([ 'meta' => $success ]);
        } else if ( $balance->saldo < 0 || $balance->saldo == 0 ) {
            $success['code'] = 300;
            $success['message'] = "Saldo anda tidak mencukupi, silahkan lakukan pengisian ulang.";

            return response()->json([ 'meta' => $success ]);
        }

        $client = new Client();

        $response = $client->request('GET', config('app.url') . "/pln/tagihan/paid-pasca.php?refID=" . $refID . "&totalTagihan=" . $totalTagihan);

        $responses[] = json_decode($response->getBody()->getContents(), true);

        $json = [];
        $data = [];

        $cost = Finance::where('kode', $code)->where('valid', 1)->first();
        $totalTagihan = $totalTagihan - ($cost->amal + $cost->keuntungan);

        DB::beginTransaction();

        try {

            foreach ( $responses as $key => $value ) {
                if ( $value['responseCode'] == 00 ) {
                    $insert = new DigiPay();
                    $insert->user_id = $userid;
                    $insert->jumlah = $totalTagihan;
                    $insert->finance_id = $cost->id;
                    $insert->akhir = $balance->saldo - ($totalTagihan + $cost->amal + $cost->keuntungan);
                    $insert->awal = $balance->saldo;
                    $insert->notes = strtoupper("Pembayaran Listrik Pasca Bayar.");
                    $insert->kode = 0;
                    $insert->valid = 1;
                    $insert->trxid = $value['refnumber'];

                    if ( $request->input('notelp') ) {
                        $insert->phone = $request->notelp;
                    }

                    $insert->save();

                    $total_amal = $cost->amal + $balance->amal;
                    $total_keuntungan = $cost->keuntungan + $balance->keuntungan;

                    $saldo = Saldo::updateOrCreate(
                        [ 'user_id' => $request->user_id ],
                        [ 'saldo' => $insert->akhir, 'amal' => $total_amal, 'keuntungan' => $total_keuntungan ]
                    );

                    DB::commit();

                    $user = User::where('id', $request->user_id)->first();

                    $data['access_token'] = [ 'token' => $request->header('Authorization') ];

                    $jml = count($value['detilTagihan']);

                    for ( $i = 0; $i < $jml; $i++ ) {

                        $json[$i]['meterAwal'] = $value['detilTagihan'][$i]['meterAwal'];
                        $json[$i]['meterAkhir'] = $value['detilTagihan'][$i]['meterAkhir'];
                        $json[$i]['periode'] = $value['detilTagihan'][$i]['periode'];
                        $json[$i]['nilaiTagihan'] = $value['detilTagihan'][$i]['nilaiTagihan'];
                        $json[$i]['denda'] = $value['detilTagihan'][$i]['denda'];
                        $json[$i]['admin'] = $value['detilTagihan'][$i]['admin'];
                        $json[$i]['total'] = $value['detilTagihan'][$i]['total'];
                        $json[$i]['fee'] = $value['detilTagihan'][$i]['fee'];

                        $data['purchas_post'] = [
                            "subscriberID"      => $value["subscriberID"],
                            "nama"              => $value["nama"],
                            "daya"              => $value["daya"],
                            "tarif"             => $value["tarif"],
                            "totalTagihan"      => $value["totalTagihan"],
                            "refnumber"         => $value["refnumber"],
                            "infoText"          => $value["infoText"],
                            "lembarTagihanSisa" => $value['lembarTagihanSisa'],
                            "lembarTagihan"     => $value['lembarTagihan'],
                            "detilTagihan"      => $json
                        ];
                    }

                    Mail::send('auth.email.plnpascabayar', compact('insert', 'user', 'responses'), function ($m) use ($user) {
                        $m->to($user->email, $user->fullname)
                          ->subject('[OBBI Application] Notification Pembayaran Listrik Pasca Bayar');
                    });

                    $success['code'] = 200;
                    $success['message'] = $value['message'];

                    return response()->json([ 'meta' => $success, 'data' => $data ]);
                } else {
                    DB::rollBack();

                    $success['code'] = $value['responseCode'];
                    $success['message'] = $value['message'];

                    return response()->json([ 'meta' => $success ]);
                }
            }
        } catch ( QueryException $e ) {
            DB::rollBack();

            $success['code'] = 401;
            $success['message'] = $e->getMessage();

            return response()->json([ 'meta' => $success ]);
        }
    }

}
