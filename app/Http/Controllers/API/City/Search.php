<?php

namespace App\Http\Controllers\API\City;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helper\Form as FM;

use App\Model\Kota;

class Search extends Controller
{
    public function __invoke(Request $request){
        (new FM)->required($request,[
            'provinsi_id',
        ]);

        $kota   = Kota::where([
            ['nama_kota','LIKE','%'.$request->nama_kota.'%'],
            ['provinsi_id','=',$request->provinsi_id]
        ])->get();

        return (new \Data)->respond([
            'access_token'  => array('token'=>$request->header('Authorization')),
            'search_kota' => $kota
        ]);
    }
}
