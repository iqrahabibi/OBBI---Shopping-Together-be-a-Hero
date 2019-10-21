<?php

namespace App\Http\Controllers\Api\Herobi;

use App\Helper\Data;
use App\Helper\ObbiAssets;
use App\Http\Controllers\Controller;
use App\Model\Herobi;
use Illuminate\Http\Request;
use Validator;

class PeriksaStatus extends Controller {
    public function __invoke (Request $request) {
        $check = Validator::make($request->input(), [
            'user_id' => [ 'required', 'exists:users,id' ]
        ], [
            'user_id.required' => 'User id harus di isi.',
            'user_id.exists'   => 'User id tersebut tidak terdaftar.'
        ]);

        if ( !$check->passes() ) {
            return [
                'meta' => [
                    'code'    => 500,
                    'message' => $check->errors()->all()[0]
                ]
            ];
        }

        $herobi = Herobi::where([
            [ 'user_id', '=', $request->input('user_id') ]
        ])->get();

        if ( empty($herobi) ) {
            return [
                'meta' => [
                    'code'    => '404',
                    'message' => 'Anda belum mengajukan dokumen.'
                ]
            ];
        }

        $data = [];
        foreach ( $herobi as $db_data ) {
            switch ( trim($db_data->valid) ) {
                case '0' :
                    $valid = "Dokumen sudah diterima.";
                    $valid_code = 0;
                    break;
                case '1' :
                    $valid = "Dokumen sudah terferifikasi.";
                    $valid_code = 1;
                    break;
                case '2':
                    $valid = "Dokumen ditolak.";
                    $valid_code = 2;
                    break;
                default :
                    $valid = "-";
                    break;
            }
            $data[] = [
                'foto_ktp'   => ObbiAssets::get_asset(ObbiAssets::USER_HEROBI, $db_data->ktp),
                'foto_kk'    => ObbiAssets::get_asset(ObbiAssets::USER_HEROBI, $db_data->kk),
                'foto_nik'   => ObbiAssets::get_asset(ObbiAssets::USER_HEROBI, $db_data->nik),
                'foto_selfi' => ObbiAssets::get_asset(ObbiAssets::USER_HEROBI, $db_data->selfi),
                'valid'      => $valid,
                'valid_code' => $valid_code   
            ];
        }

        return (new Data())->respond([
            'status' => $data
        ]);
    }
}