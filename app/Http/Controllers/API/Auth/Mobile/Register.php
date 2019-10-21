<?php

namespace App\Http\Controllers\API\Auth\Mobile;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Helper\Form as FM;

use App\Model\User;
use App\Model\Role;
use App\Model\Saldo;

use DB;

class Register extends Controller {
    public function __invoke (Request $request) {
        (new FM)->required($request, [
            'password', 'fullname', 'email', 'c_password', 'phone'
        ]);

        if ( strlen($request->password) < 6 ) {
            return [ "meta" => [ "code" => 500, "message" => "Password tidak boleh kurang dari 6 karakter." ] ];
            //throw new \SecurityExceptions('Password tidak boleh kurang dari 6 karakter.');
        }

        if ( strlen($request->phone) < 10 ) {
            return [ "meta" => [ "code" => 500, "message" => "Nomor Handphone tidak boleh kurang dari 10 digit." ] ];
            //throw new \SecurityExceptions('Nomor Handphone tidak boleh kurang dari 10 digit.');
        }

        if ( $request->password != $request->c_password ) {
            return [ "meta" => [ "code" => 500, "message" => "Password tidak sesuai." ] ];
            //throw new \SecurityExceptions('Password tidak sesuai.');
        }

        if ( $this->cekemail($request->email) > 0 ) {
            return [ "meta" => [ "code" => 500, "message" => "E-mail sudah terdaftar." ] ];
            //throw new \DataDuplicateExceptions('E-mail sudah terdaftar.','user');
        }

        $patern = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i";
        $patern2 = "/.+@(gmail|yahoo|apple|hotmail|icloud)\.com$/i";

        if ( !preg_match($patern, $request->email) ) {
            return [ "meta" => [ "code" => 500, "message" => "E-mail tidak valid." ] ];
            //throw new \SecurityExceptions('E-mail tidak valid.');
        }

        if ( !preg_match($patern2, $request->email) ) {
            return [
                "meta" => [
                    "code" => 500,
                    "message" => "Hanya e-mail @gmail.com, @yahoo.com, @apple.com, @hotmail.com, @icloud.com yang diizinkan."
                ]
            ];
            //throw new \SecurityExceptions('Hanya e-mail @gmail.com, @yahoo.com, @apple.com, @hotmail.com, @icloud.com yang diizinkan.');
        }

        DB::beginTransaction();

        $token = rand(000000, 999999);

        if ( strlen($token) < 6 ) {
            $token = $token . "" . rand(0, 9);
        }

        $user = new User();
        $user->fullname = strtoupper($request->fullname);
        $user->email = strtoupper($request->email);
        $user->password = bcrypt($request->password);
        $user->phone = $request->phone;
        $user->status = '1';
        $user->verification_token = $token;
        $user->is_verified = 0;
        $user->referal = '';

        $user->save();

        $user->roles()
             ->attach(1);

        $saldo = new Saldo();
        $saldo->user_id = $user->id;
        $saldo->saldo = 0;
        $saldo->amal = 0;
        $saldo->keuntungan = 0;

        $saldo->save();

        DB::commit();

        Mail::send('auth.email.verifikasiakun', compact('user', 'token'), function ($m) use ($user) {
            $m->to($user->email, $user->fullname)
              ->subject('Verifikasi akun OBBI');
        });

        $array = [
            'fullname' => $user->fullname, 'email' => $user->email, 'password' => $user->password,
            'image'    => $user->image, 'phone' => $user->phone, 'is_verified' => $user->is_verified
        ];

        return (new \Data)->respond([
            'user' => $array
        ]);
    }

    public function cekemail ($email) {
        $user = User::where('email', $email)
                    ->get()
                    ->count();

        return $user;
    }
}
