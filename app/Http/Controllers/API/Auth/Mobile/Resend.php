<?php

namespace App\Http\Controllers\API\Auth\Mobile;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Helper\Form as FM;

use App\Model\User;

use DB;

class Resend extends Controller {
    public function __invoke (Request $request) {
        (new FM)->required($request, [
            'email'
        ]);

        DB::beginTransaction();

        $user = User::where('email', $request->email)
                    ->first();

        $token = rand(000000, 999999);

        if ( strlen($token) < 6 ) {
            $token = $token . "" . rand(0, 9);
        }

        $user->verification_token = $token;
        $user->is_verified = 0;

        $user->save();

        DB::commit();

        Mail::send('auth.email.verifikasiakun', compact('user', 'token'), function ($m) use ($user) {
            $m->to($user->email, $user->fullname)
              ->subject('Verifikasi Akun OBBI');
        });

        return (new \Data)->respond([
            'user' => $user
        ]);
    }
}
