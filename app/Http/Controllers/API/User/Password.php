<?php

namespace App\Http\Controllers\API\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helper\Form as FM;

use App\Model\User;

use DB;
use Hash;

class Password extends Controller
{
    public function __invoke(Request $request){
        (new FM)->required($request,[
            'passwordold','passwordnew','c_passwordnew'
        ]);

        DB::beginTransaction();

        $passwordold    = User::where('id',$request->user_id)->first();

        if(!(Hash::check($request->passwordold,$passwordold->password))){
            throw new \SecurityExceptions('Password lama tidak sesuai.');
        }

        if($request->passwordnew != $request->c_passwordnew){
            throw new \SecurityExceptions('Konfirmasi password baru tidak sesuai.');
        }

        if (strlen($request->passwordnew) < 6) {
            throw new \SecurityExceptions('Password minimal 6 karakter.');
        }

        $passwordold->password = bcrypt($request->passwordnew);

        $passwordold->save();

        DB::commit();

        return (new \Data)->respond([
            'access_token'  => array('token'=>$request->header('Authorization')),
            'change_password' => $passwordold
        ]);
    }
}
