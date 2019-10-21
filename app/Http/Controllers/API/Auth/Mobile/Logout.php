<?php

namespace App\Http\Controllers\API\Auth\Mobile;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DB;

class Logout extends Controller {
    public function __invoke (Request $request) {
        $result = DB::table('oauth_access_tokens')
                    ->where('user_id', $request->user_id)
                    ->update([ 'revoked' => 1 ]);

        $success['code'] = 200;
        $success['message'] = "you has logged out.";

        return response()->json([ 'meta' => $success ]);
    }
}
