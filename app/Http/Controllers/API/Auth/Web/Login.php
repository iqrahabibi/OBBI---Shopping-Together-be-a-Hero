<?php

namespace App\Http\Controllers\API\Auth\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helper\Form as FM;

use App\Model\User;
use App\Model\Device;

use DB;
use Hash;

class Login extends Controller
{
    public function __invoke(Request $request){
        (new FM)->required($request,[
            'email','password','device','tipe'
        ]);

        $user   = User::where('email', $request->email);

        if(!(Hash::check($request->password,$user->first()->password))){
            throw new \SecurityExceptions('Password tidak sesuai.');
        }

        if(empty($user->first())){
            throw new \DataNotFoundExceptions('Data tidak ditemukan.','user');
        }

        if($user->first()->status != 1){
            throw new \SecurityExceptions('Akun anda sedang di non aktifkan, silahkan hubungi customer service kami.');
        }

        if($user->first()->is_verified != 1){
            return response()->json(['meta' => array('code' => 501,'message' => 'Anda harus aktivasi akun terlebih dahulu.')]);
        }

        $flag = true;
        foreach($user->with('roles')->first()->roles as $key => $value){
            if($value['id'] == 1){
                $flag = false;
            }
        }

        if(!$flag){
            throw new \SecurityExceptions('Anda tidak memiliki hak untuk akses halaman ini.');
        }

        $data   = $user->first()->createToken($request->email);

        DB::beginTransaction();
        
        $device = Device::updateOrCreate(
            ['user_id' => $user->first()->id, 'tipe' => $request->tipe],
            ['token_id' => $data->token->id, 'nama' => $request->device]
        );

        DB::commit();

        return (new \Data)->respond([
            'access_token'  => array('token'=>$data->accessToken),
            'user' => $user->with(['roles','detail'])->first()
        ]);
        
    }
}
