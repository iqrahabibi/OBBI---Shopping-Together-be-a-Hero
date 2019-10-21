<?php

namespace App\Http\Controllers\API\Kecamatan;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helper\Form as FM;

use App\Model\Kecamatan;

class Search extends Controller
{
    public function __invoke(Request $request){
        (new FM)->required($request,[
            'kota_id',
        ]);

        $kecamatan   = Kecamatan::where([
            ['nama_kecamatan','LIKE','%'.$request->nama_kecamatan.'%'],
            ['kota_id','=',$request->kota_id]
        ])->get();

        return (new \Data)->respond([
            'access_token'  => array('token'=>$request->header('Authorization')),
            'search_kecamatan' => $kecamatan
        ]);
    }
}
