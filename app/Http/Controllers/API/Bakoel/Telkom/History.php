<?php

namespace App\Http\Controllers\API\Bakoel\Telkom;

use App\Helper\Data;
use App\Model\DigiPay;
use App\Model\Finance;
use App\Rules\Bakoel\WhitelistCodeTelkom;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class History extends Controller {
    public function __invoke (Request $request) {
        $check = Validator::make($request->input(), [
            'user_id' => [ 'required', 'exists:users,id' ],
            'code'    => [ 'required', new WhitelistCodeTelkom() ]
        ], [
            'user_id.required' => 'User id harus di isi.',
            'uesr_id.exists'   => 'User id tidak terdaftar.',
            'code.required'    => 'Product id harus di isi.'
        ]);

        if ( $check->fails() )
            return [ 'meta' => [ 'code' => 500, 'message' => $check->errors()->all()[0] ] ];

        $f = Finance::whereRaw('LOWER(kode) = LOWER(?)', [ $request->input('code') ])->first();
        if ( empty($f) )
            return [ 'meta' => [ 'code' => 404, 'message' => 'Product id tidak terdaftar' ] ];

        $history = DigiPay::with([
            'detail_bakoel'
        ])->where([
            'user_id'    => $request->input('user_id'),
            'finance_id' => $f->id
        ])->orderBy('created_at', 'asc')->get();
        /*$jumlah_trans = 0;
        $total = 0;
        $hist = [];

        setlocale(LC_ALL, 'id_ID');
        foreach ( $history as $d ) {
            $bakoel = null;
            foreach ( $d->detail_bakoel as $b ) {
                $bakoel[] = [
                    'periode' => $b->cperiode,
                    'tagihan' => 'Rp ' . number_format($b->ntagihan, 0),
                    'admin'   => 'Rp ' . number_format($b->nadmin, 0),
                    'total'   => 'Rp ' . number_format($b->ntagihan + $b->nadmin, 0)
                ];
            }

            $hist[] = [
                'id_hist'           => $d->id,
                'tanggal_transaksi' => (trim($d->created_at) != "") ? strftime("%d %B %Y", strtotime($d->created_at)) : "--/--/--",
                'pembayaran'        => $d->notes,
                'kode_transaksi'    => $d->trxid,
                'nomor_pelanggan'   => $d->phone,
                'total'             => 'Rp ' . number_format($d->jumlah, 0),
                'detail'            => $bakoel,
            ];

            $jumlah_trans++;
            $total += $d->jumlah;
        }*/

        /*return (new Data())->respond([
            'jumlah_transaksi' => $jumlah_trans,
            'total_transaksi'  => 'Rp ' . number_format($total, 0),
            'history'          => $hist
        ]);*/

        return (new Data())->respond([
            'data' => [
                'history' => $history
            ]
        ]);
    }
}
