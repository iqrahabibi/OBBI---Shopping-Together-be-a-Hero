<?php

namespace App\Http\Controllers\API\Belanja\Ongkir;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helper\Form as FM;

use App\Model\GudangKurir;

use DB;

class Kurir extends Controller
{
    public function __invoke(Request $request){
        (new FM)->required($request,[
            'gudang_id'
        ]);

        DB::beginTransaction();

        $kurir = GudangKurir::where('gudang_id',$request->gudang_id)->get();

        $list = array();
        foreach($kurir as $key => $value){
            $list[] = $value->nama;
        }

        if(empty($kurir)){
            throw new \DataNotFoundExceptions('Data tidak ditemukan.','gudang_kurirs');
        }else{
            DB::commit();

            return (new \Data)->respond([
                'access_token'  => array('token'=>$request->header('Authorization')),
                'kurir' => $list,
            ]);
        }
    }
}
