<?php

namespace App\Http\Controllers\API\Donasi;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helper\Form as FM;

use App\Model\Donasi;
use App\Model\User;
use App\Model\DetailUser;
use App\Model\Saldo;
use Carbon\Carbon;

use DB;

class Alocation extends Controller {
    public function __invoke (Request $request) {
        (new FM)->required($request, [
            'user_id', 'alokasi'
        ]);

        $cek_tanggal = Carbon::now('Asia/Jakarta')->format('d');
        $cek_bulan_lalu = Carbon::now('Asia/Jakarta')->subMonth()->format('m');

        $detail_user = DetailUser::where([
            [ 'user_id', '=', $request->user_id ],
            [ 'valid', '=', 1 ]
        ])->first();

        // if($cek_tanggal > 10){
        //     throw new \SecurityExceptions('Pengalokasian amal asik hanya tanggal 1 - 10 setiap bulan.');
        // }

        DB::beginTransaction();

        $cek_donasi = Donasi::where('detail_user_id', $detail_user->id)
                            ->whereMonth('created_at', $cek_bulan_lalu)->count();

        if ( $cek_donasi < 0 ) {
            //throw new \DataNotFoundExceptions('Tidak ada alokasi dana pada bulan lalu.', 'donasi');
            return [
                'meta' => [
                    'code'    => 500,
                    'message' => 'Tidak ada alokasi dana pada bulan lalu'
                ]
            ];
        }

        $cek_saldo = Saldo::where('user_id', $request->user_id)->first();

        $total_alokasi = 0;

        $jumlah_alokasi = $request->alokasi;

        foreach ( $jumlah_alokasi['jumlah'] as $key => $value ) {
            $total_alokasi = $total_alokasi + $value['alokasi'];
        }

        if ( $cek_saldo->amal < $total_alokasi ) {
            // throw new \SecurityExceptions('Jumlah disalurkan terlalu besar dari saldo amal yang anda miliki.');
            return [
                'meta' => [
                    'code'    => 500,
                    'message' => 'Jumlah disalurkan terlalu besar dari saldo amal yang anda miliki.'
                ]
            ];
        }

        if ( $cek_saldo->amal < 1 ) {
            // throw new \SecurityExceptions('Anda tidak memiliki saldo amal yang cukup.');
            return [
                'meta' => [
                    'code'    => 500,
                    'message' => 'Anda tidak memiliki saldo amal yang cukup.'
                ]
            ];
        }

        if ( $cek_saldo->amal != $total_alokasi ) {
            // throw new \SecurityExceptions('Jumlah pengalokasian amal harus sama dengan jumlah saldo amal anda.');
            return [
                'meta' => [
                    'code'    => 500,
                    'message' => 'Jumlah pengalokasian amal harus sama dengan jumlah saldo amal anda.'
                ]
            ];
        }

        if ( empty($detail_user) ) {
            // throw new \SecurityExceptions('Anda belom melengkapi biodata diri.');
            return [
                'meta' => [
                    'code'    => 500,
                    'message' => 'Anda belom melengkapi biodata diri.'
                ]
            ];
        }

        foreach ( $jumlah_alokasi['jumlah'] as $key => $value ) {

            $donasi = new Donasi();
            $donasi->target_donasi_id = $value['target_id'];
            $donasi->detail_user_id = $detail_user->id;
            $donasi->awal = $cek_saldo->amal;
            $donasi->jumlah = $value['alokasi'];
            $donasi->akhir = $cek_saldo->amal - $value['alokasi'];

            $donasi->save();
        }

        $cek_saldo->amal = 0;

        if ( $cek_saldo->save() ) {
            DB::commit();

            $success['code'] = 200;
            $success['message'] = "Anda berhasil melakukan alokasi amal.";

            return response()->json([ 'meta' => $success ]);
        } else {
            DB::rollBack();

            $success['code'] = 401;
            $success['message'] = "Data failed.";

            return response()->json([ 'meta' => $success ]);
        }

    }
}
