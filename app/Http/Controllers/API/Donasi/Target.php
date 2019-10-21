<?php

namespace App\Http\Controllers\API\Donasi;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\TargetDonasi;

class Target extends Controller
{
    public function __invoke(Request $request){
        $target_donasi = TargetDonasi::with('tipe_donasi','agama')->get();

        return (new \Data)->respond([
            'access_token'  => array('token'=>$request->header('Authorization')),
            'target_donasi' => $target_donasi
        ]);
    }
}
