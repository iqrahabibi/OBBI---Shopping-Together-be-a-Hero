<?php

namespace App\Http\Controllers\API\Barang;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helper\Form as FM;
use Illuminate\Pagination\LengthAwarePaginator;

use App\Model\OMerchantBarangGrosir;
use App\Model\OMerchantBarangInventory;
use App\Model\DetailUser;
use App\Model\Barang;
use App\Model\User;

use DB;
use Auth;

class ReadDaerah extends Controller
{
    public function __invoke(Request $request){
        $user_id    = User::with('detail.kelurahan.kecamatan')->where('id',$request->user_id)
        ->first();

        $barang_daerah  = new OMerchantBarangInventory();

        $searchable = $barang_daerah->get_searchable();
        $orderable  = $barang_daerah->get_orderable();

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
            $barang_daerah   = $barang_daerah->where(function ($query) use ($request, $searchable)
            {
                foreach($searchable as $search)
                {
                    $query->orwhere($search, 'like', "%$request->search%");
                }
            });
        }

        $url    = config('app.api').'/storage';
        $data   = array();
        $gambar = array();
        $harga  = array(); 

        $barang_daerah  = $barang_daerah->with('usaha_om.usaha.kelurahan.kecamatan',
        'barang.om_barang_grosir','barang.om_gambar','barang.varian_om','barang.category')
        ->whereHas('usaha_om',function($query) use ($user_id){
            $query->whereHas('usaha',function($query2) use ($user_id){
                $query2->whereHas('kelurahan',function($query3) use ($user_id){
                    $query3->whereHas('kecamatan', function($query4) use ($user_id){
                        $query4->where('id',$user_id->detail->kelurahan->kecamatan->id);
                    });
                });
            });
        })
        ->where('qty','>',0)
        ->orderby($orderby,$ordertype)->orderby('urut','asc')->get();

        $a = array();

        foreach($barang_daerah as $key => $value){
            $keuntungan = 0;
            $amal       = 0;

            if(!empty($value->barang->keuntungan)){

                $keuntungan = $value->barang->keuntungan;
            }

            if(!empty($value->barang->jumlah_amal)){

                $amal       = $value->barang->jumlah_amal;
            }

            if(in_array($value->barang_id,$a)){
                continue;
            }

            $a[] = $value->barang_id;

            if(!empty($value->barang->om_gambar)){
                foreach($value->barang->om_gambar as $key2 => $value2){
                    $gambar[$key2] = $url.$value2->gambar_barang;
                }
            }

            if(!empty($value->barang->om_barang_grosir)){
                $indexKey2 = 0;
                foreach($value->barang->om_barang_grosir as $key2 => $value2){
                    if($value->kode_usaha != $value2->kode_usaha){
                        continue;
                    }
                    $harga[$indexKey2]['harga']  = $value2->harga_jual+$amal+$keuntungan;
                    $harga[$indexKey2]['qty']    = $value2->qty;
                    $harga[$indexKey2]['varian'] = $value2->varian->varian_barang;
                    $harga[$indexKey2]['varian_id']= $value2->varian->id;
                    
                    $indexKey2++;
                }
            }

            $data[] = array(
                "belanja_id"   => $value->id,
                "kode_usaha"    => $value->kode_usaha,
                "qty"           => $value->qty,
                "harga_jual"    => $harga,
                "gudang_id"     => null,
                "gudang_nama"   => null,
                "nama_usaha"    => $value->usaha_om->usaha->nama_usaha,
                "alamat"        => $value->usaha_om->usaha->alamat,
                "wilayah"       => array(
                    "kecamatan" => $value->usaha_om->usaha->kelurahan->kecamatan->nama_kecamatan,
                    "kelurahan" => $value->usaha_om->usaha->kelurahan->nama_kelurahan,
                ),
                "barang_id"     => $value->barang->id,
                "barang_nama"   => $value->barang->nama_barang,
                "weight"        => $value->barang->weight,
                "deskripsi"     => $value->barang->deskripsi,
                "sku"           => $value->barang->sku,
                "brand"         => $value->barang->brand,
                "nama_kategori" => $value->barang->category->nama_kategori,
                "nama_group"    => $value->barang->category->group->nama_group,
                "url_gambar"    => $gambar,
                "type"          => "lokal",
            );
        }

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $itemCollection = collect($data);
        $perPage = $show;
        $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->all();
        
        $fixPageItem = array();
        foreach($currentPageItems as $item){
            $fixPageItem[] = $item;
        }
        $paginatedItems= new LengthAwarePaginator($fixPageItem , count($itemCollection), $perPage);
        $paginatedItems->setPath($request->url());
    
        $success['code'] = 200;
        $success['message'] = 'Berhasil';

        return response()->json([
            'meta' => $success,
            'access_token' => array('token'=>$request->header('Authorization')),
            'produk_daerah' => $paginatedItems,
            'option' => [
                'searchable' => $searchable,
                'orderable' => $orderable
            ]
        ]);
    }
}
