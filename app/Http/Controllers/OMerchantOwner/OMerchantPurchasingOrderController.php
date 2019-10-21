<?php

namespace App\Http\Controllers\OMerchantOwner;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Html\Builder;
use App\Helper\Formatting;
use App\OBBI\obbiHelper as DATA;

use App\Model\OMerchant;
use App\Model\OMerchantPo;
use App\Model\OMerchantPoDetail;
use App\Model\OMerchantPoMasuk;
use App\Model\OMerchantPoRetur;
use App\Model\OMerchantAdmin;
use App\Model\User;

use DataTables;
use Session;
use Auth;
use DB;

class OMerchantPurchasingOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function monitoring(Request $request, Builder $htmlBuilder)
    {
        if($request->ajax()){
            $omerchantpo  = OMerchantPo::with('usaha_o_merchant.usaha', 'gudang')->get();

            return Datatables::of($omerchantpo)
            ->addColumn('usaha_o_merchant',function($data){
                if(empty($data->usaha_o_merchant->usaha)){
                    return '';
                }
                return $data->usaha_o_merchant->usaha->nama_usaha;
            })
            // ->addColumn('nomor_po',function($data){
            //     return $data->usaha_o_merchant->kode;
            // })
            ->addColumn('gudang',function($data){
                return $data->gudang->nama_gudang;
            })
            ->addColumn('tanggal',function($data){
                return $data->tanggal;
            })
            ->addColumn('total',function($data){
                return Formatting::rupiah($data->total);
            })
            ->addColumn('action',function($data){
                return view('omerchantowner.omerchant._action_monitoring',[
                    'model' =>$data,
                    'detail_url' => route('omerchantowner.monitoringdetil',$data->id),
                ]);
            })
            ->rawColumns([
                'match', 'action'
            ])
            ->make(true);
        }

        $html=$htmlBuilder
            ->addColumn(['data'=>'usaha_o_merchant','name'=>'usaha_o_merchant','title'=>'Usaha'])
            // ->addColumn(['data'=>'usaha_o_merchant','name'=>'usaha_o_merchant','title'=>'Usaha'])
            ->addColumn(['data'=>'gudang','name'=>'gudang','title'=>'Gudang'])
            ->addColumn(['data'=>'tanggal','name'=>'tanggal','title'=>'Tanggal Purchasing Order'])
            ->addColumn(['data'=>'total','name'=>'total','title'=>'Total Harga'])
            ->addColumn(['data'=>'status','name'=>'status','title'=>'Status'])
            ->addColumn(['data'=>'action','name'=>'action','title'=>'Action','orderable'=>false,'searchable'=>false]);

        return view('omerchantowner.omerchant.monitoring')->with(compact('html'));
    }

    public function monitoringdetil(Request $request, Builder $htmlBuilder, $id)
    {
        if($request->ajax()){
            $omerchantpo  = OMerchantPoDetail::with('o_merchant_po.usaha_o_merchant.usaha','barang_grosir')->where('o_merchant_po_id',$id)->get();

            return Datatables::of($omerchantpo)
            ->addColumn('o_merchant_po',function($data){    
                return $data->o_merchant_po->usaha_o_merchant->usaha->nama_usaha;
            })
            ->addColumn('barang',function($data){
                return $data->barang_grosir->barang->nama_barang;
            })
            ->addColumn('jumlah',function($data){
                return $data->jumlah;
            })
            ->addColumn('harga',function($data){
                return Formatting::rupiah($data->harga);
            })
            ->make(true);
        }

        $html=$htmlBuilder
            ->addColumn(['data'=>'o_merchant_po','name'=>'o_merchant_po','title'=>'Nama Usaha'])
            ->addColumn(['data'=>'barang','name'=>'barang','title'=>'Nama Barang'])
            ->addColumn(['data'=>'jumlah','name'=>'jumlah','title'=>'Jumlah'])
            ->addColumn(['data'=>'harga','name'=>'harga','title'=>'Harga']);

        return view('omerchantowner.omerchant.detail')->with(compact('html'));
    }
}
