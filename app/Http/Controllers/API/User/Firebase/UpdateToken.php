<?php

namespace App\Http\Controllers\API\User\Firebase;

use App\Helper\Data;
use App\Http\Controllers\Controller;
use App\Model\DetailUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UpdateToken extends Controller {
    public function __invoke (Request $request) {
        $check = Validator::make($request->input(), [
            'user_id'        => [ 'required' ],
            'token_firebase' => [ 'required' ]
        ], [
            'user_id.required'        => 'User id harus di isi.',
            'token_firebase.required' => 'Token firebase harus di isi.'
        ]);

        if ( $check->fails() ) {
            return [
                'meta' => [
                    'code'    => 500,
                    'message' => $check->errors()->all()[0]
                ]
            ];
        }

        $detail = DetailUser::where([
            [ 'user_id', '=', $request->input('user_id') ],
            [ 'valid', '=', '1' ]
        ])->first();

        if ( empty($detail) ) {
            return [
                'meta' => [
                    'code'    => 404,
                    'message' => 'User id tersebut tidak ada.'
                ]
            ];
        }

        DB::beginTransaction();
        $update = DetailUser::where([
            [ 'user_id', '=', $request->input('user_id') ],
            [ 'valid', '=', '1' ]
        ])->update([
            'firebase'   => $request->input('token_firebase'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        if ( $update ) {
            DB::commit();

            return [
                'meta' => [
                    'code'    => 200,
                    'message' => 'Data berhasil di simpan.'
                ]
            ];
        } else {
            DB::rollBack();

            return [
                'meta' => [
                    'code'    => 500,
                    'message' => 'Tidak dapat menyimpan token firebase.'
                ]
            ];
        }
    }
}