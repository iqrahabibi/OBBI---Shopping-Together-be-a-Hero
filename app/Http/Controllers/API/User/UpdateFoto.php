<?php

namespace App\Http\Controllers\API\User;

use App\Helper\Data;
use App\Helper\FileUploader;
use App\Helper\ObbiAssets;
use App\Model\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Kreait\Firebase\Storage;
use Validator;

class UpdateFoto extends Controller {
    public function __invoke (Request $request) {
        $check = Validator::make($request->input(), [
            'user_id' => 'required'
        ], [
            'user_id.required' => 'User id harus di isi.'
        ]);

        if ( !$check->passes() ) {
            return [
                'meta' => [
                    'code'    => 500,
                    'message' => $check->errors()->all()[0]
                ]
            ];
        }

        if ( !$request->hasFile('image') ) {
            return [
                'meta' => [
                    'code'    => 500,
                    'message' => 'Anda belum mengirimkan foto.'
                ]
            ];
        }

        $file = $request->file('image');
        if ( !in_array($file->getMimeType(), [ 'image/jpeg', 'image/jpg', 'image/png' ]) ) {
            return [
                'meta' => [
                    'code'    => 500,
                    'message' => 'Format foto tidak sesuai.'
                ]
            ];
        }

        $user = User::where([
            [ 'id', '=', $request->input('user_id') ]
        ])->first();

        if ( empty($user) ) {
            return [
                'meta' => [
                    'code'    => 400,
                    'message' => 'User id tersebut tidak di temukan'
                ]
            ];
        }

        $upload = $helper = (new FileUploader(7559, FileUploader::USER_PROFILE, 'image'))
            ->setMime([
                'image/jpeg', 'image/jpg', 'image/png'
            ])
            ->doUpload($request);

        if ( $upload['meta']['code'] == 200 ) {
            DB::beginTransaction();
            if ( !empty($user->image) ) {
                ObbiAssets::delete_asset(ObbiAssets::USER_PROFILE, $user->image);
            }
            $user->image = $upload['data']['path'];
            $user->save();
            DB::commit();

            return (new Data())->respond([
                'foto_user' => ObbiAssets::get_asset(ObbiAssets::USER_PROFILE, $upload['data']['path'])
            ]);
        } else {
            return [
                'meta' => [
                    'code'    => 500,
                    'message' => $upload['meta']['message']
                ]
            ];
        }
    }
}
