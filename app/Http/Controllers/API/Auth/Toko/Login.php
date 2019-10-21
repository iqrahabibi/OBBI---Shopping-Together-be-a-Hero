<?php

namespace App\Http\Controllers\API\Auth\Toko;

use App\Helper\Data;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helper\Form as FM;

use App\Model\User;
use App\Model\Role;
use App\Model\DetailUser;
use App\Model\OMerchantAdmin;

use Auth;
use DB;
use Validator;
use Hash;

class Login extends Controller {
    public function __invoke (Request $request) {
        (new FM)->required($request, [
            'password', 'email'
        ]);

        DB::beginTransaction();

        $user = User::where('email', $request->email);

        if ( empty($user->first()) ) {
            // throw new \DataNotFoundExceptions('Data tidak ditemukan.', 'user');
            return [
                'meta' => [
                    'code'    => 500,
                    'message' => trans('data.not-found')
                ]
            ];
        }

        if ( !(Hash::check($request->password, $user->first()['password'])) ) {
            // throw new \SecurityExceptions('Password tidak sesuai.');
            return [
                'meta' => [
                    'code'    => 500,
                    'message' => trans('user.wrong-upass')
                ]
            ];
        }

        if ( $user->first()->status != 1 ) {
            // throw new \SecurityExceptions('Akun anda sedang di non aktifkan, silahkan hubungi customer service kami.');
            return [
                'meta' => [
                    'code'    => 500,
                    'message' => trans('user.user-banned')
                ]
            ];
        }

        if ( $user->first()->is_verified != 1 ) {
            /*return response()->json([
                'meta' => [
                    'code'    => 501,
                    'message' => 'Anda harus aktivasi akun terlebih dahulu.'
                ]
            ]);*/
            return [
                'meta' => [
                    'code'    => 500,
                    'message' => trans('user.not-activated')
                ]
            ];
        }

        $cek_om_admin = OMerchantAdmin::with('usaha_o_merchant')
                                      ->where('user_id', $user->first()->id)
                                      ->first();

        if ( empty($cek_om_admin) ) {
            // throw new \DataNotFoundExceptions('Anda bukan omerchant admin.', 'user');
            return [
                'meta' => [
                    'code'    => 500,
                    'message' => 'not-omerchant-admin'
                ]
            ];

        } else if ( $cek_om_admin->usaha_o_merchant ) {
            // throw new \SecurityExceptions('Anda tidak memiliki akses toko.');
            return [
                'meta' => [
                    'code'    => 500,
                    'message' => trans('toko.doesnt-have-access')
                ]
            ];
        }

        $data = $user->with('detail', 'roles')
                     ->first();
        $datas = $data->createToken($request->email);
        DB::commit();

        return (new Data())->respond([
            'access_token' => [ 'token' => $datas->accessToken ],
            'user'         => $data,
        ]);
    }
}
