<?php

namespace App\Http\Controllers\API\Barang;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;

use App\Model\BarangNasional;
use App\Model\BarangInventory;
use App\Model\Barang;

use DB;

class Nasional extends Controller
{
    public function __invoke(Request $request){
        
        $nasional   = new BarangInventory();

        $searchable = $nasional->get_searchable();
        $orderable  = $nasional->get_orderable();

        $nasional   = $nasional->with('barang')->where('qty','>',0);

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
                $success['code']    = 401;
                $success['message'] = "can't ordering by $request->orderby";

                return response()->json(['meta'=> $success]);
            }

            $orderby = $request->orderby;
        }

        if($request->input('ordertype'))
        {
            $ordertype  = $request->ordertype;
        }

        // dd($nasional);

        if($request->input('search'))
        {
            $nasional   = $nasional->where(function ($query) use ($request, $searchable)
            {
                foreach($searchable as $search)
                {
                    $query->orwhere($search, 'like', "%$request->search%");
                }
            });
        }

        $nasional               = $nasional->with(
            ['barang.gambar','gudang','barang.category','barang.varian','barang.barang_nasional'])
        ->whereHas('barang',function($query){
            $query->whereHas('barang_nasional');
        })
        ->get();
        
        $url    = config('app.api').'/storage';
        $data   = array();
        $gambar = array();
        $b_nasional = array();

        foreach($nasional as $key => $value){
            $keuntungan = 0;
            $amal       = 0;

            if(!empty($value->barang->keuntungan)){

                $keuntungan = $value->barang->keuntungan;
            }

            if(!empty($value->barang->jumlah_amal)){

                $amal       = $value->barang->jumlah_amal;
            }
            if(!empty($value->barang->gambar)){
                foreach($value->barang->gambar as $key2 => $value2){
                    $gambar[$key2] = $url.$value2->gambar_barang;
                }     
            }

            if(!empty($value->barang->barang_nasional)){
                $indexKey2 = 0;
                foreach($value->barang->barang_nasional as $key2 => $value2){
                    if($value->gudang_id != $value2->gudang_id){
                        continue;
                    }
                    $b_nasional[$indexKey2]['harga'] = $value2->harga_jual+$amal+$keuntungan;
                    $b_nasional[$indexKey2]['qty']   = $value2->qty;
                    $b_nasional[$indexKey2]['varian']= $value2->varian->varian_barang;
                    $b_nasional[$indexKey2]['varian_id']= $value2->varian->id;

                    $indexKey2++;
                }
            }

            

            $data[] = array(
                "belanja_id"   => $value->id,
                "kode_usaha"    => null,
                "qty"           => $value->qty,
                "harga_jual"    => $b_nasional,
                "gudang_id"     => $value->gudang->id,
                "gudang_nama"   => $value->gudang->nama_gudang,
                "nama_usaha"    => null,
                "alamat"        => $value->gudang->alamat,
                "barang_id"     => $value->barang->id,
                "barang_nama"   => $value->barang->nama_barang,
                "weight"        => $value->barang->weight,
                "deskripsi"     => $value->barang->deskripsi,
                "wilayah"       => null,
                "sku"           => $value->barang->sku,
                "brand"         => $value->barang->brand,
                "nama_kategori" => $value->barang->category->nama_kategori,
                "nama_group"    => $value->barang->category->group->nama_group,
                "url_gambar"    => $gambar,
                "type"          => "nasional",
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
            'produk_nasional' => $paginatedItems,
            'option' => [
                'searchable' => $searchable,
                'orderable' => $orderable
            ]
        ]);
    }
}
