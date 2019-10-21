<?php

namespace App\Http\Controllers\API\Auth\Mobile;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helper\Form as FM;

use App\Model\User;
use App\Model\Role;
use App\Model\Saldo;
use App\Model\Device;

use DB;

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Firebase\Auth\Token\Exception\InvalidToken;

class RegisterGmail extends Controller {
    public function __invoke (Request $request) {
        (new FM)->required($request, [
            'password', 'fullname', 'email', 'phone', 'token_gmail', 'uid', 'tipe', 'device'
        ]);
        if ( $this->cekemail($request->email) > 0 ) {
            return [ "meta" => [ "code" => 500, "message" => "E-mail sudah terdaftar." ] ];
            //throw new \DataDuplicateExceptions('E-mail sudah terdaftar.','user');
        }
        $serviceAccount = ServiceAccount::fromJsonFile(storage_path() . '/firebase/obbi-android-v2-firebase-adminsdk-ve25j-7304d7baae.json');
        $firebase = (new Factory)
            ->withServiceAccount($serviceAccount)
            ->create();

        $userfirebase = $firebase->getAuth()
                                 ->getUser($request->uid);

        try {
            if ( $userfirebase->email != $request->email ) {
                // throw new \SecurityExceptions('Token tidak dikenali dari email anda.');
                return [
                    'meta' => [
                        'code'    => 500,
                        'message' => 'Token tidak dikenali dari email anda.'
                    ]
                ];
            }

            $firebase->getAuth()
                     ->verifyIdToken($request->token_gmail);

        } catch ( InvalidToken $e ) {
            // throw new \SecurityExceptions($e->getMessage());
            return [
                'meta' => [
                    'code'    => 500,
                    'message' => $e->getMessage()
                ]
            ];
        }

        DB::beginTransaction();

        $cek = User::where('email', $request->email)
                   ->first();
        if ( !empty($cek) ) {
            // throw new \DataDuplicateExceptions('Email sudah terdaftar.', 'email');
            return [
                'meta' => [
                    'code'    => 500,
                    'message' => 'Email sudah terdaftar.'
                ]
            ];
        }

        $user = User::create(array_merge($request->all(), [
            'password'    => bcrypt($request->password),
            'email'       => strtoupper($request->email),
            'status'      => '1',
            'is_verified' => 1,
        ]));

        if ( !empty($request->image) ) {
            $user->image = $request->image;
        }

        $user->save();

        $user->roles()
             ->attach(1);

        $saldo = new Saldo();
        $saldo->user_id = $user->id;
        $saldo->saldo = 0;
        $saldo->amal = 0;
        $saldo->keuntungan = 0;

        $saldo->save();

        $data = $user->createToken($request->email);
        Device::updateOrCreate(
            [ 'user_id' => $user->id, 'tipe' => $request->tipe ],
            [ 'token_id' => $data->token->id, 'nama' => $request->device ]
        );

        Device::updateOrCreate(
            [ 'user_id' => $user->id, 'tipe' => $request->tipe ],
            [ 'token_id' => $data->token->id, 'nama' => $request->device ]
        );
        DB::commit();

        return (new \Data)->respond([
            'access_token' => [ 'token' => $data->accessToken ],
            'user'         => $user->where('email', $request->email)
                                   ->with([ 'roles', 'detail', 'saldo' ])
                                   ->first()
        ]);
    }

    public function cekemail ($email) {
        $user = User::where('email', strtolower($email))
                    ->get()
                    ->count();

        return $user;
    }
}
