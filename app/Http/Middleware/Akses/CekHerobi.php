<?php

namespace App\Http\Middleware\Akses;

use Closure;

use App\Model\User;

class CekHerobi {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return mixed
     */
    public function handle ($request, Closure $next) {
        $user = User::with('herobi')->where('id', $request->user_id)->first();

        if ( empty($user->herobi) ) {
            $success['code'] = 203;
            $success['message'] = "Anda belum mengajukan dokumen herobi, silahkan ajukan.";

            return response()->json([ 'meta' => $success ]);
        } else if ( !empty($user->herobi) && $user->herobi->valid == 0 ) {
            $success['code'] = 201;
            $success['message'] = "Pengajuan Anda sedang di proses.";

            return response()->json([ 'meta' => $success ]);
        } else if ( !empty($user->herobi) && $user->herobi->valid == 2 ) {
            $success['code'] = 202;
            $success['message'] = "Pengajuan Anda ditolak.";

            return response()->json([ 'meta' => $success ]);
        }

        return $next($request);
    }
}
