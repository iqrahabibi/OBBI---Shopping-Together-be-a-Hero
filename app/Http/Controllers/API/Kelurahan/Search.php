<?php

namespace App\Http\Controllers\API\Kelurahan;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helper\Form as FM;

use App\Model\Kelurahan;

class Search extends Controller
{
    public function __invoke(Request $request){
        (new FM)->required($request,[
            'kecamatan_id',
        ]);

        $kelurahan = Kelurahan::where([
            ['nama_kelurahan','LIKE','%'.$request->nama_kelurahan.'%'],
            ['kecamatan_id','=',$request->kecamatan_id]
        ])->get();

        return (new \Data)->respond([
            'access_token'  => array('token'=>$request->header('Authorization')),
            'search_kelurahan' => $kelurahan
        ]);
    }
}
