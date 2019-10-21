<?php

namespace App\Http\Controllers\API\Saldo;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\User;
use App\Model\DigiPay;
use App\Model\Saldo;
use \Illuminate\Support\Facades\DB;
use Validator;

class Check_saldo extends Controller {
    public function __invoke (Request $request) {
        $check = Validator::make($request->input(), [
            'user_id' => 'required'
        ], [
            'user_id.required' => 'User id harus di isi.'
        ]);

        if ( !$check->passes() ) {
            return [
                'meta' => [
                    'code'    => 403,
                    'message' => $check->errors()->all()[0]
                ]
            ];
        }

        $user = User::where(
            [
                [ 'id', '=', $request->input('user_id') ]
            ]
        )->first();

        if ( empty($user) ) {
            return [
                'meta' => [
                    'code'    => 404,
                    'message' => 'User tersebut tidak ditemukan.'
                ]
            ];
        }

        $saldo = Saldo::where(
            [
                [ 'user_id', '=', $request->input('user_id') ]
            ]
        )->first();

        if ( empty($saldo) ) {
            // buat baru kalo ga ada
            $digipay = DB::table('digi_pays')
                         ->where([
                             [ 'user_id', '=', $user->id ],
                             [ 'digi_pays.kode', '=', 0 ],
                             [ 'digi_pays.valid', '=', 1 ]
                         ])
                         ->join('finances', function ($join) {
                             $join->on('finances.id', '=', 'digi_pays.finance_id');
                         })
                         ->select([
                             'digi_pays.jumlah', 'finances.keuntungan', 'finances.amal'
                         ])
                         ->get();

            if ( @count($digipay) == 0 ) {
                DB::beginTransaction();
                // kalo di digipays null
                DB::table('saldos')->insert([
                    'user_id'    => $user->id,
                    'saldo'      => 0,
                    'amal'       => 0,
                    'keuntungan' => 0,
                    'created_at' => Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s')
                ]);
                DB::commit();
            } else {
                $saldo = 0;
                $amal = 0;
                $keuntungan = 0;

                foreach ( $digipay as $data ) {
                    $saldo += $data->jumlah;
                    $keuntungan += $data->keuntungan;
                    $amal += $data->amal;
                }

                DB::table('saldos')->insert([
                    'user_id'    => $user->id,
                    'saldo'      => $saldo,
                    'amal'       => $amal,
                    'keuntungan' => $keuntungan,
                    'created_at' => Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s')
                ]);

                return [ $digipay ];
            }

            // select ulang biar lebih pasti
            $saldo = Saldo::where(
                [
                    [ 'user_id', '=', $request->input('user_id') ]
                ]
            )->first();
        }

        return [
            'meta' => [
                'code'    => 200,
                'message' => ''
            ],
            'data' => [
                'saldo' => [
                    'saldo'      => $saldo->saldo,
                    'amal'       => $saldo->amal,
                    'keuntungan' => $saldo->keuntungan,
                ]
            ]
        ];
    }
}
