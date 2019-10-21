<?php

namespace App\Http\Controllers\API\Barang;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\OMerchantBarangGrosir;
use App\Model\Barang;
use App\Model\Herobi;
use App\Model\BarangDaerahNasional;

use DB;
use Auth;

class ReadAll extends Controller
{
    public function __invoke(Request $request){

        $omerchantbaranggrosir  = new BarangDaerahNasional();

        $searchable = $omerchantbaranggrosir->get_searchable();
        $orderable  = $omerchantbaranggrosir->get_orderable();

        $orderby    = 'id';
        $ordertype  = 'asc';
        $show       = 10;

        if($request->input('show'))
        {
            $show = $request->show;
        }

        if($request->input('orderby'))
        {
            if(!in_array($request->orderby,$orderable))
            {
                throw new \OptionException("can't ordering by $request->orderby");
            }

            $orderby = $request->orderby;
        }

        if($request->input('ordertype'))
        {
            $ordertype  = $request->ordertype;
        }

        if($request->input('search'))
        {
            $omerchantbaranggrosir   = $omerchantbaranggrosir->where(function ($query) use ($request, $searchable)
            {
                foreach($searchable as $search)
                {
                    $query->orwhere($search, 'like', "%$request->search%");
                }
            });
        }

        $cek_herobi  = Herobi::where([
            ['user_id','=', $request->user_id],
            ['valid','=',1]
        ])->first();
        $url    = config('app.api');

        if(empty($cek_herobi)){
            $omerchantbaranggrosir  = $omerchantbaranggrosir->with('nasional','daerah')->where('nasional_id','!=',null)->orderby($orderby,$ordertype)->groupby('id')->paginate($show);

            foreach($omerchantbaranggrosir as $key => $value){

                if(!empty($value->nasional->barang->gambar)){

                    foreach($value->nasional->barang->gambar as $key2 => $value2){

                        $value2->url_barang = $url.$value2->gambar_barang;

                    }

                }
            }
        }else{
            $omerchantbaranggrosir  = $omerchantbaranggrosir->with('nasional','daerah')->groupby('id')->orderby($orderby,$ordertype)->groupby('id')->paginate($show);

            foreach($omerchantbaranggrosir as $key => $value){

                if(!empty($value->nasional->barang->gambar)){
                    foreach($value->nasional->barang->gambar as $key2 => $value2){
                        
                        $value2->url_barang = $url.$value2->gambar_barang;
                    }
                }

                if(!empty($value->daerah->barang->gambar)){
                    foreach($value->daerah->barang->gambar as $key2 => $value2){
                        
                        $value2->url_barang = $url.$value2->gambar_barang;
                    }
                }
            }
        }

    
        return response()->json([
            'access_token' => array('token'=>$request->header('Authorization')),
            'produk' => $omerchantbaranggrosir,
            'option' => [
                'searchable' => $searchable,
                'orderable' => $orderable
            ]
        ]);
    }
}
