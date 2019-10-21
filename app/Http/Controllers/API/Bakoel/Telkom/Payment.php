<?php

namespace App\Http\Controllers\API\Bakoel\Telkom;

use App\Model\DetailTransaksiBakoel;
use App\Rules\Bakoel\WhitelistCodeTelkom;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use GuzzleHttp\Client;
use App\Helper\Form as FM;
use App\Model\DigiPay;
use App\Model\Saldo;
use App\Model\User;
use App\Model\Finance;
use Illuminate\Support\Facades\Validator;
use Mockery\CountValidator\AtMost;

class Payment extends Controller {
    //    public function __invoke (Request $request) {
    //        (new FM)->required($request, [
    //            'refID', 'total', 'user_id', 'code', 'phone'
    //        ]);
    //
    //        $balance = Saldo::where('user_id', $request->user_id)->first();
    //
    //        $refId = $request->refID;
    //        $total = $request->total;
    //
    //        if ( $balance->saldo < $total ) {
    //            $success['code'] = 300;
    //            $success['message'] = "Saldo anda tidak mencukupi, silahkan lakukan pengisian ulang.";
    //
    //            return response()->json([ 'meta' => $success ]);
    //        }
    //
    //        if ( $balance->saldo < 0 || $balance->saldo == 0 ) {
    //            $success['code'] = 300;
    //            $success['message'] = "Saldo anda tidak mencukupi, silahkan lakukan pengisian ulang.";
    //
    //            return response()->json([ 'meta' => $success ]);
    //        }
    //
    //        $client = new Client();
    //
    //        $response = $client->request('POST', config("app.url") . "/telkom/payment.php", [
    //            "form_params" => [
    //                "refID" => $refId,
    //                "code"  => "TELKOM",
    //                "total" => $total,
    //            ]
    //        ]);
    //
    //        $responses[] = json_decode($response->getBody()->getContents(), true);
    //
    //        $data = [];
    //
    //        $cost = Finance::where('kode', $request->code)->where('valid', 1)->first();
    //        $total = $total - ($cost->amal + $cost->keuntungan);
    //
    //        DB::beginTransaction();
    //
    //        try {
    //            foreach ( $responses as $key => $value ) {
    //                if ( $value['responseCode'] == 00 ) {
    //                    $insert = new DigiPay();
    //                    $insert->user_id = $request->user_id;
    //                    $insert->jumlah = $total;
    //                    $insert->finance_id = $cost->id;
    //                    $insert->awal = $balance->saldo;
    //                    $insert->akhir = $balance->saldo - ($total + $cost->amal + $cost->keuntungan);
    //                    $insert->notes = strtoupper("pembayaran TELKOM.");
    //                    $insert->kode = 0;
    //                    $insert->valid = 1;
    //                    $insert->trxid = $value['idpel'];
    //                    $insert->phone = $request->phone;
    //
    //                    $insert->save();
    //
    //                    $total_amal = $cost->amal + $balance->amal;
    //                    $total_keuntungan = $cost->keuntungan + $balance->keuntungan;
    //
    //                    $saldo = Saldo::updateOrCreate(
    //                        [ 'user_id' => $request->user_id ],
    //                        [ 'saldo' => $insert->akhir, 'amal' => $total_amal, 'keuntungan' => $total_keuntungan ]
    //                    );
    //
    //                    DB::commit();
    //
    //                    $user = User::where('id', $request->user_id)->first();
    //
    //                    Mail::send('auth.email.telkom', compact('insert', 'user', 'responses'), function ($m) use ($user) {
    //                        $m->to($user->email, $user->fullname)
    //                          ->subject('[OBBI Application] Notification Pembayaran PDAM');
    //                    });
    //
    //                    $data['access_token'] = [ 'token' => $request->header('Authorization') ];
    //
    //                    $data['telkom'] = $value;
    //
    //                    $success['code'] = 200;
    //                    $success['message'] = $value['message'];
    //
    //                    return response()->json([ 'meta' => $success, 'data' => $data ]);
    //
    //                } else {
    //                    DB::rollBack();
    //
    //                    $success['code'] = $value['responseCode'];
    //                    $success['message'] = $value['message'];
    //
    //                    return response()->json([ 'meta' => $success ]);
    //                }
    //            }
    //        } catch ( QueryException $e ) {
    //            DB::rollBack();
    //
    //            $success['code'] = 401;
    //            $success['message'] = $e->getMessage();
    //
    //            return response()->json([ 'meta' => $success ]);
    //        }
    //    }
    public function __invoke (Request $request) {
        $check = Validator::make($request->input(), [
            'refID'   => [ 'required' ],
            'total'   => [ 'required' ],
            'user_id' => [ 'required' ],
            'code'    => [ 'required', new WhitelistCodeTelkom() ],
            /*'phone'   => [ 'required' ]*/
        ], [
            'refid.required'   => 'Reference Id harus di isi.',
            'total.required'   => 'Total pembayaran harus di isi.',
            'user_id.required' => 'User id harus di isi.',
            'code.required'    => 'Kode pembayaran harus di isi',
            /*'phone.required'   => trans('validation.required')*/
        ]);

        if ( $check->fails() )
            return [ 'meta' => [ 'code' => 500, 'message' => $check->errors()->all()[0] ] ];

        $saldo = Saldo::where([
            'user_id' => $request->input('user_id')
        ])->first();

        if ( empty($saldo) )
            return [ 'meta' => [ 'code' => 500, 'message' => trans('user.saldo.kurang') ] ];

        if ( ($saldo->saldo <= 0) || ($saldo->saldo < $request->input('total')) )
            return [ 'meta' => [ 'code' => 500, 'message' => trans('user.saldo.kurang') ] ];

        $client = (new Client())->request('POST', config('app.url') . "/telkom/payment.php", [
            // WARNING : don't touch !!
            "form_params" => [
                'security_question' => hash('sha256', date('Ymd') . md5("wkwkwk{$request->input('total')}jangan{$request->input('refID')}kepo") . ":)"),
                'product_id'        => ($request->input('code') == "TSPEEDY") ? "TELKOMSPEEDY" : $request->input('code'),
                'references_id'     => $request->input('refID'),
                'total_payment'     => $request->input('total')
            ]
        ]);

        $json = json_decode($client->getBody()->getContents());
        if ( json_last_error() != JSON_ERROR_NONE || !isset($json->responseCode) )
            return [ 'meta' => [ 'code' => 500, 'message' => 'Tidak dapat mengambil data dari server.' ] ];

        if ( $json->responseCode != 00 )
            return [ 'meta' => [ 'code' => 500, 'message' => "Transaksi gagal : {$json->message}" ] ];

        $finance = Finance::where([
            [ 'kode', '=', $request->input('code') ]
        ])->first();

        if ( empty($finance) )
            return [ 'meta' => [ 'code' => 500, 'message' => trans('form.kode_bayar.not_found') ] ];

        $total = $request->input('total') - ($finance->amal + $finance->keuntungan);

        DB::beginTransaction();
        $x = ($request->input('code') == "TSPEEDY") ? "SPEEDY/INDIHOME" : "TELKOM";
        $insert = new DigiPay();
        $insert->user_id = $request->user_id;
        $insert->jumlah = $total;
        $insert->finance_id = $finance->id;
        $insert->awal = $saldo->saldo;
        $insert->akhir = $saldo->saldo - $total;
        $insert->notes = "PEMBAYARAN " . $x;
        $insert->kode = 0;
        $insert->valid = 1;
        $insert->trxid = $request->input('refID');
        $insert->phone = $request->input('phone');

        $x_row = count($json->tagihan);
        $x_act = 0;

        if ( $insert->save() ) {
            foreach ( $json->tagihan as $d ) {
                $tagihan = 0;
                if ( isset($d->nilaiTagihan) )
                    $tagihan = $d->nilaiTagihan;

                if ( isset($d->nilai_tagihan) )
                    $tagihan = $d->nilai_tagihan;

                $detail = new DetailTransaksiBakoel();
                $detail->digipay_id = $insert->id;
                $detail->cperiode = $d->periode;
                $detail->ntagihan = $tagihan;
                $detail->nadmin = $d->admin;
                if ( $detail->save() ) {
                    $x_act++;
                }
            }
        }

        if ( $x_act == $x_row ) {
            $total_a = $finance->amal + $saldo->amal;
            $total_k = $finance->keuntungan + $saldo->keuntungan;

            $update = Saldo::updateOrCreate(
                [ 'user_id' => $request->input('user_id') ],
                [ 'saldo' => $insert->akhir, 'amal' => $total_a, 'keuntungan' => $total_k ]
            );

            if ( $update ) {
                DB::commit();
                $user = User::where('id', $request->input('user_id'))->first();

                Mail::send('auth.email.telkom', compact('insert', 'user', 'json'), function ($m) use ($user, $x) {
                    $m->to($user->email, $user->fullname)->subject("[OBBI Application] Notifikasi Pembayaran {$x}");
                });

                $tagihan = [];
                foreach ( $json->tagihan as $data ) {
                    /// tagihan
                    $xtagihan = 0;
                    if ( isset($data->nilaiTagihan) )
                        $xtagihan = $data->nilaiTagihan;

                    if ( isset($data->nilai_tagihan) )
                        $xtagihan = $data->nilai_tagihan;

                    /// admin
                    $admin = 0;
                    if ( isset($data->admin) )
                        $admin = $data->admin;

                    /// total
                    $total = 0;
                    if ( isset($data->total) )
                        $total = $data->total;

                    $fee = 0;
                    if ( isset($data->fee) )
                        $fee = $data->fee;


                    $tagihan[] = [
                        'periode'       => $data->periode,
                        'nilai_tagihan' => (int) $xtagihan,
                        'admin'         => (int) $admin,
                        'total'         => (int) $total,
                        'fee'           => (int) $fee
                    ];
                }

                return [
                    'meta' => [
                        'code'    => 200,
                        'message' => 'Pembayaran berhasil.'
                    ],
                    'data' => [
                        'id_pelanggan'   => $json->idpel,
                        'ref_id'         => $request->input('refID'),
                        'kode_area'      => $json->kodeArea,
                        'jumlah_tagihan' => $json->jumlahTagihan,
                        'nama_pelanggan' => $json->nama,
                        'total_tagihan'  => $json->totalTagihan,
                        'tagihan'        => $tagihan
                    ]
                ];
            } else {
                return [ 'meta' => [ 'code' => 500, 'message' => "Transaksi gagal!!" ] ];
            }

        } else {
            DB::rollBack();

            return [ 'meta' => [ 'code' => 500, 'message' => "Transaksi gagal!" ] ];
        }
    }
}
