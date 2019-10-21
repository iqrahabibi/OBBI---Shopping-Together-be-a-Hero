<?php

namespace App\Http\Controllers\Transaksi;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Html\Builder;

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

class OMerchantAdminPurchasingOrderController extends Controller
{
    public function index(Request $request, Builder $htmlBuilder){
        
        if($request->ajax()){
            $user = User::find(Auth::user()->id);
            $om_admin = OMerchantAdmin::where('user_id', $user->id)->select('gudang_id')->get();
    
            $list = array();
            foreach ($om_admin as $index => $om) {
                $list[] = $om->gudang_id;
            }
    
            $omerchantpo  = OMerchantPo::with('usaha_o_merchant.usaha', 'gudang')
                ->whereIn('gudang_id', $list)->get();

            return Datatables::of($omerchantpo)
            ->addColumn('usaha_o_merchant',function($data){
                return $data->usaha_o_merchant->usaha->nama_usaha;
            })
            ->addColumn('gudang',function($data){
                return $data->gudang->nama_gudang;
            })
            ->addColumn('tanggal',function($data){
                return $data->tanggal;
            })
            ->addColumn('total',function($data){
                return $data->total;
            })
            ->addColumn('action',function($data){
                return view('transaksi.omerchantadminpo._action',[
                    'model' =>$data,
                    'checked_url' =>route('omerchantadminpo.verify',['id'=>$data->id,'status'=>'Checked']),
                    'rejected_url' =>route('omerchantadminpo.verify',['id'=>$data->id,'status'=>'Rejected']),
                    'detail_url' => route('omerchantadminpo.show',$data->id),
                ]);
            })
            ->make(true);
        }

        $html=$htmlBuilder
            ->addColumn(['data'=>'usaha_o_merchant','name'=>'usaha_o_merchant','title'=>'Usaha'])
            ->addColumn(['data'=>'gudang','name'=>'gudang','title'=>'Gudang'])
            ->addColumn(['data'=>'tanggal','name'=>'tanggal','title'=>'Tanggal Purchasing Order'])
            ->addColumn(['data'=>'total','name'=>'total','title'=>'Total Harga'])
            ->addColumn(['data'=>'status','name'=>'status','title'=>'Status'])
            ->addColumn(['data'=>'action','name'=>'action','title'=>'Action','orderable'=>false,'searchable'=>false]);

        return view('transaksi.omerchantadminpo.index')->with(compact('html'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Builder $htmlBuilder, $id)
    {
        if($request->ajax()){
            $user = User::find(Auth::user()->id);
            $om_admin = OMerchantAdmin::where('user_id', $user->id)->select('gudang_id')->get();
    
            $list = array();
            foreach ($om_admin as $index => $om) {
                $list[] = $om->gudang_id;
            }
    
            $omerchantpo  = OMerchantPoDetail::with('o_merchant_po.usaha_o_merchant.usaha','barang_grosir')
                ->where('o_merchant_po_id',$id)->get();

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
                return $data->harga;
            })
            ->make(true);
        }

        $html=$htmlBuilder
            ->addColumn(['data'=>'o_merchant_po','name'=>'o_merchant_po','title'=>'Nama Usaha'])
            ->addColumn(['data'=>'barang','name'=>'barang','title'=>'Nama Barang'])
            ->addColumn(['data'=>'jumlah','name'=>'jumlah','title'=>'Jumlah'])
            ->addColumn(['data'=>'harga','name'=>'harga','title'=>'Harga']);

        return view('transaksi.omerchantadminpo.detail')->with(compact('html'));
    }

    public function verify(Request $request, $id, $status){
        DB::beginTransaction();

        $data = OMerchantPo::find($id);
        $data->status = $status;

        if($data->update()){
            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>"Data successfully ".$status."."
            ]);
        }else{
            DB::rollBack();

            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be ".$status."."
            ]);
        }
        return redirect()->route('omerchantadminpo.index');
    }
}
