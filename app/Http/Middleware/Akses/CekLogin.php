<?php

namespace App\Http\Middleware\Akses;

use Closure;
use App\Model\User;
use App\Model\Device;

use DB;

class cekLogin {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return mixed
     */
    public function handle ($request, Closure $next) {
        $cek = User::where('email', $request->email)
                   ->first();

        if ( !empty($cek) ) {
            $device = Device::where([
                [ 'user_id', '=', $cek->id ],
                [ 'tipe', '=', $request->tipe ]
            ])
                            ->first();

            $result = DB::table('oauth_access_tokens as a')
                        ->join('devices as b', 'b.token_id', '=', 'a.id')
                        ->join('users as c', 'b.user_id', 'c.id')
                        ->where([
                            [ 'a.user_id', '=', $cek->id ],
                            [ 'revoked', '=', '0' ],
                        ])
                        ->count();

            if ( $result >= 1 && !empty($device) ) {
                DB::beginTransaction();

                try {
                    $update = DB::table('oauth_access_tokens')
                                ->where([
                                    [ 'id', '=', $device->token_id ],
                                    [ 'user_id', '=', $cek->id ],
                                    [ 'revoked', '=', '0' ]
                                ])
                                ->update([ 'revoked' => 1 ]);

                    DB::commit();

                } catch ( QueryException $e ) {
                    return response()->json([ 'meta' => [ 'code' => 401, 'message' => $e->getMessage() ] ]);
                }

            }

        } else {
            return response()->json([ 'meta' => [ 'code' => 500, 'message' => 'Akun belum terdaftar.' ] ]);
        }

        return $next($request);
    }
}
