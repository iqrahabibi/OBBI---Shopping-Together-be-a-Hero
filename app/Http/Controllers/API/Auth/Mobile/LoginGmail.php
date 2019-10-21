<?php

namespace App\Http\Controllers\API\Auth\Mobile;

use App\Helper\Data;
use App\Helper\ObbiAssets;
use App\Model\Opf;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helper\Form as FM;

use App\Model\User;
use App\Model\Device;
use App\Model\Role;
use App\Model\DetailUser;
use App\Model\Herobi;

use DB;

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Firebase\Auth\Token\Exception\InvalidToken;
use Validator;

class LoginGmail extends Controller {
    public function __invoke (Request $request) {
        $check = Validator::make($request->input(), [
            'email'  => 'required|email',
            'device' => 'required',
            'tipe'   => 'required',
            'uid'    => 'required'
        ], [
            'email.required'  => trans('form.email-required'),
            'email.email'     => trans('form.email-email'),
            'device.required' => trans('form.device-required'),
            'tipe.required'   => trans('form.tipe-required'),
            'uid.required'    => trans('form.uid-required'),
        ]);

        if ( !$check->passes() ) {
            return [
                'meta' => [
                    'code'    => 500,
                    'message' => $check->errors()
                                       ->all()[0]
                ]
            ];
        }

        $sa = ServiceAccount::fromJsonFile(storage_path() . '/firebase/obbi-android-v2-firebase-adminsdk-ve25j-7304d7baae.json');
        $firebase = (new Factory())
            ->withServiceAccount($sa)
            ->create();

        $userfirebase = $firebase->getAuth()
                                 ->getUser($request->input('uid'));

        // where condition untuk memeriksa email yang dikirimkan user
        $user = User::with([ 'detail', 'roles_2', 'detail.agama' ])
                    ->where([
                        [ 'email', '=', $request->input('email') ]
                    ])
                    ->first();

        // ketika email yang dikirimkan tidak terdaftar di dalam database.
        if ( empty($user) ) {
            return [
                'meta' => [
                    'code'    => 500,
                    'message' => trans('form.email-not-found')
                ]
            ];
        }

        // ketika account user tidak aktif
        if ( $user->status != 1 ) {
            return [
                'meta' => [
                    'code'    => 500,
                    'message' => trans('user.user-banned')
                ]
            ];
        }

        try {
            if ( strtolower($userfirebase->email) != strtolower($user->email) ) {
                return [
                    'meta' => [
                        'code'    => 500,
                        'message' => 'Token tidak dikenali dari email anda.'
                    ]
                ];
            }

            $user->token_gmail = $request->input('token_gmail');
            $user->update();

        } catch ( InvalidToken $e ) {
            return [
                'meta' => [
                    'code'    => 500,
                    'message' => $e->getMessage()
                ]
            ];
        }

        // buat token untuk oauth per email
        $token = $user->createToken($request->input('email'));

        Device::updateOrCreate(
            [
                'user_id' => $user->id,
                'tipe'    => $request->input('tipe')
            ],
            [
                'token_id' => $token->token->id,
                'nama'     => $request->input('device')
            ]
        );
        DB::commit();

        if ( substr($user->image, 0, 5) != 'https' ) {
            $user->image = ObbiAssets::get_asset(ObbiAssets::USER_PROFILE, $user->image);
        }

        $roles = [];
        foreach ( $user->roles_2 as $roles_data ) {
            if ( !empty($roles_data->role) ) {
                $roles[] = [
                    'id'   => $roles_data->role->id,
                    'name' => $roles_data->role->name
                ];
            }
        }

        // check herobi
        $herobi = ( !is_null(Herobi::where([
            [ 'valid', '=', '1' ], [ 'user_id', '=', $user->id ]
        ])->first()));

        // cari data opf
        $opf = Opf::where(
            [
                [ 'user_id', '=', $user->id ],
                [ 'valid', '=', '1' ]
            ])->with('referal_opf')->first();

        $x_opf = null;
        if ( !empty($opf) ) {
            $referal = null;
            if ( !empty($opf->referal_opf) ) {
                foreach ( $opf->referal_opf as $x_referal_opf ) {
                    $referal[] = [
                        'user_id' => $x_referal_opf->user_id,
                        'id_opf'  => $x_referal_opf->opf_id,
                        'nama'    => ucwords(strtolower($x_referal_opf->user->fullname)),
                        'email'   => strtolower($x_referal_opf->user->email)
                    ];
                }
            }
            $x_opf = [
                'foto'             => trim($opf->foto),
                'nomor_hp'         => trim($opf->handphone),
                'referal'          => trim($opf->referal),
                'pengguna_referal' => $referal
            ];
        }

        $detail = null;
        $agama = null;
        $kelurahan = null;
        if ( !empty($user->detail) ) {
            $x_detail = $user->detail;
            if ( !empty($x_detail->agama) ) {
                $agama = [
                    'id'         => $x_detail->agama->id,
                    'nama_agama' => $x_detail->agama->nama_agama
                ];
            }

            if ( !empty($x_detail->kelurahan) ) {
                $x_kelurahan = $x_detail->kelurahan;
                $kelurahan = [
                    "id"             => (int) $x_kelurahan->id,
                    "kecamatan_id"   => (int) $x_kelurahan->kecamatan_id,
                    "nama_kelurahan" => trim($x_kelurahan->nama_kelurahan),
                    "kode_pos"       => trim($x_kelurahan->kode_pos),
                ];

                if ( !empty($x_detail->kelurahan->kecamatan) ) {
                    $x_kecamatan = $x_kelurahan->kecamatan;
                    $kelurahan['kecamatan'] = [
                        "id"             => (int) $x_kecamatan->id,
                        "kota_id"        => (int) $x_kecamatan->kota_id,
                        "nama_kecamatan" => trim($x_kecamatan->nama_kecamatan),
                    ];

                    if ( !empty($x_kecamatan->kota) ) {
                        $x_kota = $x_kecamatan->kota;
                        $kelurahan['kecamatan']['kota'] = [
                            "id"          => (int) $x_kota->id,
                            "tipe"        => trim($x_kota->tipe),
                            "nama_kota"   => trim($x_kota->nama_kota),
                            "provinsi_id" => (int) $x_kota->provinsi_id,
                        ];

                        if ( !empty($x_kota->provinsi) ) {
                            $x_provinsi = $x_kota->provinsi;
                            $kelurahan['kecamatan']['kota']['provinsi'] = [
                                "id"            => (int) $x_provinsi->id,
                                "nama_provinsi" => trim($x_provinsi->nama_provinsi)
                            ];
                        }

                    }
                }
            }
        }

        if ( !is_null($agama) && !is_null($kelurahan) )
            $detail = [ 'agama' => $agama, 'kelurahan' => $kelurahan ];

        return [
            'meta' => [
                'code' => 200
            ],
            'data' => [
                'access_token'  => [
                    'token' => $token->accessToken
                ],
                'user'          => [
                    'id'       => (int) $user->id,
                    "fullname" => trim(ucwords($user->fullname)),
                    "email"    => trim(strtolower($user->email)),
                    "phone"    => trim($user->phone),
                    "image"    => trim($user->image),
                    "referal"  => trim($user->referal),
                    'detail'   => $detail,
                    'roles'    => $roles
                ],
                'opf'           => $x_opf,
                'status_herobi' => $herobi
            ]
        ];
    }
}
