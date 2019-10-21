<?php

namespace App\Http\Controllers\API\OPF;

use App\Helper\Data;
use App\Helper\ObbiAssets;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\Opf;
use App\Model\User;
use App\Model\DetailUser;

use DB;

class Profile extends Controller {
    public function __invoke (Request $request) {

        DB::beginTransaction();

        $data_kelurahan = DetailUser::where([
            [ 'user_id', '=', $request->user_id ],
            [ 'valid', '=', 1 ]
        ])->first();

        if ( empty($data_kelurahan) ) {
            // throw new \DataNotFoundExceptions('Detail user masih kosong.', 'opf');
            return [
                'meta' => [
                    'code'    => 404,
                    'message' => 'Detail user masih kosong'
                ]
            ];
        } else {
            $data_opf = DB::table('opfs as a')
                          ->join('users as b', 'a.user_id', '=', 'b.id')
                          ->join('detail_users as c', 'c.user_id', '=', 'b.id')
                          ->join('kelurahans as d', 'c.kelurahan_id', '=', 'd.id')
                          ->where([
                              [ 'c.kelurahan_id', '=', $data_kelurahan->kelurahan_id ],
                              [ 'c.valid', '=', 1 ],
                          ])
                          ->select([
                              'a.referal', 'b.fullname', 'd.nama_kelurahan', 'd.kode_pos', 'a.handphone', 'b.email',
                              'c.alamat', 'a.foto'
                          ])
                          ->first();
        }

        DB::commit();

        if ( !is_null($data_opf) )
            $data_opf->foto = ObbiAssets::get_asset(ObbiAssets::USER_OPF, $data_opf->foto);

        return (new Data())->respond([
            'access_token' => $request->header('Authorization'),
            'data_opf'     => $data_opf
        ]);
    }
}
