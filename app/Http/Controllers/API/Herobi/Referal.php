<?php

namespace App\Http\Controllers\API\Herobi;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use App\Model\User;
use App\Model\Herobi;
use App\Model\ReferalHerobi;
use Auth;
use Validator;
use DB;



class Referal extends Controller {
    public function __invoke (Request $request) {
        $user = User::findOrFail($request->post('user_id'));

        $herobi = Herobi::where([
            [ 'user_id', $user->id ],
            [ 'valid', '=', 1 ]
        ])->first();

        $show = 10;
        $success['code'] = 200;
        $data = [];
        if ( $herobi ) {
            // $contoh = DB::table('referal_herobis','herobis','users')
            //             ->join('herobis','referal_herobis.herobi_id','=','herobis.id')
            //             ->join('users','herobis.user_id','=','users.id')
            //             ->where([
            //                 ['referal_herobis.user_id', $herobi->user_id],
            //                 ['referal_herobis.valid', '=', 1],
            //                 ['herobis.valid','=',1]
            //             ])
            //             ->select()
            //             ->get();
            $referalherobis = ReferalHerobi::with('herobi')->where([
                [ 'user_id', $herobi->user_id ],
                [ 'valid', '=', 1 ],
                [ 'valid', '=', 1 ]
            ]);

            $data['access_token']   = [ 'token' => $request->header('Authorization') ];
            $data['herobis']        = $referalherobis->with('herobi.user')->paginate($show);

            return response()->json([
                'meta' => $success,
                'data' => $data
            ]);
        } else {
            return response()->json([
                'meta' => $success,
                'data' => $data
            ]);
        }
    }
}
