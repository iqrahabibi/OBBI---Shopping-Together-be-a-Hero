<?php

namespace App\Http\Middleware\Akses;

use Closure;
use App\Model\User;
use App\Model\Userbalance;

class checkPin {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return mixed
     */
    public function handle ($request, Closure $next) {
        //        $user = User::with('roles')
        //                    ->where('id', $request->user_id);

        //        $herobi = false;
        //        foreach ( $user->with('roles')
        //                       ->all() as $key => $value ) {
        //            /*if($value['id'] != 1 ){
        //                throw new \SecurityExceptions('Anda bukan Herobi.');
        //            }*/
        //            $herobi = ($value['id'] == 1);
        //            if ( $herobi )
        //                break;
        //        }

        // if($request->input('invoice'))
        // {
        //     $invoice= DigiPay::where([
        //         ['invoice','=',$request->invoice],
        //         ['valid','=',0]
        //     ])->with('user')->first();

        //     if($invoice->user->user_type == 2)
        //     {
        //         $success['code']    = 230;
        //         $success['message'] = "Hai ".$user->fullname.", ". $invoice->user->fullname." tidak dapat melakukan transaksi ini karena belum menjadi Herobi.";

        //         return response()->json(['meta'=> $success]);
        //     }
        // }

        $roles = User::with('roles')
                     ->where('id', $request->user_id)
                     ->first();

        $herobi = false;
        if ( isset($roles->roles) ) {
            foreach ( $roles->roles as $data ) {
                if ( $data->id == 1 ) {
                    $herobi = true;
                    break;
                }
            }
        }

        if ( !$herobi ) {
            return [
                'meta' => [
                    'code'    => 500,
                    'message' => 'Anda bukan herobi'
                ]
            ];
        }

        return $next($request);
    }
}
