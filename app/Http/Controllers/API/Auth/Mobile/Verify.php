<?php

namespace App\Http\Controllers\API\Auth\Mobile;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Helper\Form as FM;

use App\Model\User;
use App\Model\DetailUser;
use App\Model\Device;

use DB;

class Verify extends Controller {
    public function __invoke (Request $request) {
        (new FM)->required($request, [
            'code', 'tipe', 'device'
        ]);

        DB::beginTransaction();

        $check = User::where('verification_token', $request->code)
                     ->first();

        if ( !empty($check) ) {
            $check->is_verified = 1;
            $check->verification_token = null;

            $check->save();

            $data = $check->createToken($check->email);

            Device::updateOrCreate(
                [ 'user_id' => $check->first()->id, 'tipe' => $request->tipe ],
                [ 'token_id' => $data->token->id, 'nama' => $request->device ]
            );

            Mail::send('auth.email.afterverifikasi', compact('check'), function ($m) use ($check) {
                $m->to($check->email, $check->fullname)
                  ->subject('Selamat bergabung di OBBI');
            });

            DB::commit();

            return (new \Data)->respond([
                'access_token' => [ 'token' => $data->accessToken ],
                'user'         => [ 'data' => $check ]
            ]);
        } else {
            // throw new \DataNotFoundExceptions('Data tidak ditemukan.', 'user');
            return [
                'code'    => 500,
                'message' => 'Data tidak ditemukan.'
            ];
        }
    }
}
