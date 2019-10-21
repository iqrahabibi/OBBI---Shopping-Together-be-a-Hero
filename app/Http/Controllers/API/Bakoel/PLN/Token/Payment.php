<?php

namespace App\Http\Controllers\API\Bakoel\PLN\Token;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use GuzzleHttp\Client;
use App\Helper\Form as FM;

use App\Model\Saldo;
use App\Model\DigiPay;
use App\Model\Finance;
use App\Model\User;

use DB;

class Payment extends Controller {
    public function __invoke (Request $request) {
        (new FM)->required($request, [
            'refID', 'powerPurchaseUnsold', 'user_id', 'code', 'type'
        ]);

        $balance = Saldo::where('user_id', $request->user_id)->first();

        $refID = $request->refID;
        $powerPurchase = $request->powerPurchaseUnsold;

        if ( $balance->saldo < $powerPurchase ) {
            $success['code'] = 300;
            $success['message'] = "Saldo anda tidak mencukupi, silahkan lakukan pengisian ulang.";

            return response()->json([ 'meta' => $success ]);
        }

        if ( $balance->saldo < 0 || $balance->saldo == 0 ) {
            $success['code'] = 300;
            $success['message'] = "Saldo anda tidak mencukupi, silahkan lakukan pengisian ulang.";

            return response()->json([ 'meta' => $success ]);
        }

        $client = new Client();

        $response = $client->request('GET', config('app.url') . "/pln/token/paid-pre.php?refID=" . $refID . "&powerPurchaseUnsold=" . $powerPurchase);

        $responses[] = json_decode($response->getBody()->getContents(), true);

        $json = [];
        $data = [];
        $string2['angka'] = [];
        $content = "";

        $cost = Finance::where('kode', $request->code)->first();
        $powerPurchase = $powerPurchase - ($cost->amal + $cost->keuntungan);

        DB::beginTransaction();

        try {
            foreach ( $responses as $key => $value ) {

                if ( $request->type == 0 ) {
                    $content = "Pembelian Token Listrik UNSOLD";
                } else if ( $request->type == 1 ) {
                    $content = "Pembelian Token Listrik DENOM";
                }

                if ( $value['responseCode'] == 00 ) {
                    $number = $value['data']['tokenNumber'];
                    $array = array_map('intval', str_split($number));
                    $jml = count($array);
                    $j = 1;

                    for ( $i = 0; $i < $jml; $i++ ) {
                        $string = "";
                        if ( $j % 4 == 0 ) {
                            $string = $array[$i] . " " . $string;
                            $j = 0;
                        } else {
                            $string = $array[$i] . $string;
                        }

                        $string2['angka'][] = $string;
                        $j++;
                    }

                    $g = str_replace(",", "", str_replace("]", "", str_replace("[", '', str_replace('"', '', json_encode($string2['angka'])))));

                    $insert = new DigiPay();
                    $insert->user_id = $request->user_id;
                    $insert->jumlah = $powerPurchase;
                    $insert->finance_id = $cost->id;
                    $insert->awal = $balance->saldo;
                    $insert->akhir = $balance->saldo - ($powerPurchase + $cost->amal + $cost->keuntungan);
                    $insert->notes = strtoupper($content);
                    $insert->kode = 0;
                    $insert->valid = 1;
                    $insert->trxid = $value['data']['ref'];
                    $insert->tipe_token = $request->type;
                    $insert->token_number = $value['data']['tokenNumber'];

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

                    Mail::send('auth.email.plntoken', compact('insert', 'user', 'responses', 'g'), function ($m) use ($user) {
                        $m->to($user->email, $user->fullname)
                          ->subject('[OBBI Application] Notification Pembayaran Listrik Token');
                    });

                    $data['access_token'] = [ 'token' => $request->header('Authorization') ];

                    $data['prepaid'] = [ 'data'     => $value['data'], "totalTagihan" => $value['totalTagihan'],
                                         "infotext" => $value['infotext']
                    ];

                    $success['code'] = 200;
                    $success['message'] = $value['message'];

                    return response()->json([ 'meta' => $success, 'data' => $data ]);
                } else if ( $value['responseCode'] == 9983 ) {
                    $insert = new DigiPay();
                    $insert->user_id = $request->user_id;
                    $insert->jumlah = $powerPurchase;
                    $insert->finance_id = $cost->id;
                    $insert->awal = $balance->saldo;
                    $insert->notes = strtoupper($content);
                    $insert->kode = 0;
                    $insert->valid = 1;
                    $insert->trxid = $value['manualAdviceHashID'];
                    $insert->tipe_token = $request->type;

                    if ( $request->input('notelp') ) {
                        $insert->phone = $request->notelp;
                    }

                    $insert->save();

                    $saldo = Saldo::updateOrCreate(
                        [ 'user_id' => $request->user_id ],
                        [ 'saldo' => $insert->akhir, 'amal' => $total_amal, 'keuntungan' => $total_keuntungan ]
                    );

                    // $result2->save();

                    DB::commit();

                    $user = User::where('id', $request->user_id)->first();

                    // Mail::send('auth.email.plntoken',compact('insert','user','responses','g'),function($m) use ($user){
                    //     $m->to($user->email,$user->fullname)->subject('[OBBI Application] Notification Pembayaran Listrik Token');
                    // });

                    $success['code'] = $value['responseCode'];
                    $success['message'] = $value['message'];
                    $success['manualid'] = $value['manualAdviceHashID'];

                    return response()->json([ 'meta' => $success ]);
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
