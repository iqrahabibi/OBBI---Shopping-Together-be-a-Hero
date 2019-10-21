<?php

namespace App\Http\Controllers\API\OPF;

use App\Helper\Data;
use App\Http\Controllers\Controller;
use App\Model\User;
use Illuminate\Http\Request;
use Validator;

class ListReferal extends Controller {
    public function __invoke (Request $request) {
        $check = Validator::make($request->input(), [
            'user_id' => [ 'required', 'exists:users,id' ]
        ], [
            'user_id.required' => 'User id harus di isi.',
            'user_id.exists'   => 'User id tidak terdaftar.'
        ]);

        if ( !$check->passes() ) {
            return [
                'meta' => [
                    'code'    => 500,
                    'message' => $check->errors()->all()[0]
                ]
            ];
        }

        $user = User::with([
            'referal_opf' => function ($query) use ($request) {
                $query->where('opfs.user_id', $request->input('user_id'));
                $query->where('opfs.valid', '1');
            }
        ])->where([
            [ 'id', '=', $request->input('user_id') ],
        ])->first();

        if ( empty($user) ) {
            return [
                'meta' => [
                    'code'    => 500,
                    'message' => 'Maaf, Anda tidak memiliki role OPF'
                ]
            ];
        }

        $opf = null;
        if ( !is_null($user->referal_opf) ) {
            foreach ( $user->referal_opf->referal_opf as $x_opf ) {
                $opf[] = [
                    'user_id' => $x_opf->user->id,
                    'nama'    => ucwords(strtolower($x_opf->user->fullname)),
                    'email'   => $x_opf->user->email
                ];
            }
        };

        /*foreach ( $user->referal_opf as $x_opf ) {
            $x_pengguna = null;
            foreach ( $x_opf->referal_opf as $pengguna ) {
                $xuser = User::where([
                    [ 'id', '=', $pengguna->user_id ],
                    [ 'status', '=', '1' ]
                ])->first();

                if ( !empty($xuser) ) {
                    $x_pengguna[] = [
                        'user_id' => $pengguna->user_id,
                        'nama'    => ucwords(strtolower($xuser->fullname)),
                        'email' => $pengguna->email
                    ];
                }
            }
            $opf[] = [
                'id_opf'           => $x_opf->id,
                'user_id'          => $x_opf->user_id,
                'foto'             => $x_opf->foto,
                'nomor_hp'         => $x_opf->handphone,
                'referal'          => $x_opf->referal,
                'pengguna_referal' => $x_pengguna
            ];
        }*/

        return (new Data())->respond([
            /*'id'               => $user->id,
            'nama_lengkap'     => trim($user->fullname),
            'email'            => strtolower(trim($user->email)),
            'telp'             => $user->phone,
            'foto'             => $user->image,
            'referal'          => $user->referal,*/
            'pengguna_referal' => $opf

        ]);
    }
}
