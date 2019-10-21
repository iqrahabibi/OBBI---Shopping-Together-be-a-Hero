<?php

namespace App\Http\Controllers\API\Amal;

use App\Helper\Data;
use App\Model\Donasi;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class Saldo extends Controller {
    public function all (Request $request) {
        $result = Donasi::with('user')->where('jumlah', '<', 0)->get();

        $total = 0;
        $totalProvince = 0;
        $totalKota = 0;
        $totalKecamatan = 0;
        $totalKelurahan = 0;

        foreach ( $result as $key => $value ) {
            // foreach($value->user as $key2 => $value2){//user
            // foreach($value2->detail as $key3 => $value3){//detail
            //     // cek kelurahan
            //     // if($value2->kelurahan_id == $value3->id){
            //     //     $totalKelurahan+=$value->jumlah;
            //     // }
            //     // foreach($value3->kelurahan as $key4 => $value4){
            //     //     // Cek kecamatan
            //     //     if($value3->subdistrict_id == $value4->id){
            //     //         $totalKecamatan+=$value->jumlah;
            //     //     }
            //     //     foreach($value4->kecamatan as $key5 => $value5){
            //     //         // cek kota
            //     //         if($value4->city_id == $value5->id){
            //     //             $totalKota+=$value->jumlah;
            //     //         }
            //     //     }
            //     // }
            // }

            // }
            $total += $value->jumlah;
        }

        $success['code'] = 200;

        $data['access_token'] = [ 'token' => $request->header('Authorization') ];
        $data['all'] = $result;
        $data['totalsaldo'] = abs($total);

        return response()->json([ 'meta' => $success, 'data' => $data ]);
    }

    public function pribadi (Request $request) {
        $result = Donasi::where([
            [ 'jumlah', '<', 0 ],
            [ 'user_id', '=', $request->input('user_id') ]
        ])->with('user')->get();

        $total = 0;
        foreach ( $result as $key => $value )
            $total += $value->jumlah;


        return (new Data())->respond([
            'access_token' => [
                'token' => $request->header('Authorization')
            ],
            'pribadi'      => $result,
            'totalsaldo'   => abs($total)
        ]);
    }
}
