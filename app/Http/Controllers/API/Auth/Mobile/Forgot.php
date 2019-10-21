<?php

namespace App\Http\Controllers\API\Auth\Mobile;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Helper\Form as FM;

use App\Model\User;

use DB;

class Forgot extends Controller {
    public function __invoke (Request $request) {
        (new FM)->required($request, [
            'email'
        ]);

        DB::beginTransaction();

        $check = User::where('email', $request->email)
                     ->count();

        if ( $check < 1 ) {
            return [
                'meta' => [
                    'code'    => 500,
                    'message' => 'E-mail belum terdaftar.'
                ]
            ];
        }

        $newpass = str_random(15);
        $user = User::where('email', $request->email)
                    ->first();
        $user->password = bcrypt($newpass);

        Mail::send('auth.email.resetpassword', compact('user', 'newpass'), function ($m) use ($user) {
            $m->to($user->email, $user->fullname)
              ->subject('Reset Password OBBI');
        });

        $message = 'Maaf, perubahan password gagal. Silahkan coba lagi.';
        if ( !(count(Mail::failures()) > 0) && $user->save() ) {
            DB::commit();
            $message = 'Berhasil ubah password. Silahkan periksa email Anda.';
        }

        return (new \Data)->respond([
            'user'    => $user,
            'message' => $message
        ]);
    }
}
