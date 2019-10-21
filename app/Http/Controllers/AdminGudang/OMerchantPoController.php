<?php

namespace App\Http\Controllers\AdminGudang;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Html\Builder;

use App\Model\OMerchant;
use App\Model\OMerchantPo;
use App\Model\OMerchantPoDetail;
use App\Model\OMerchantPoMasuk;
use App\Model\OMerchantPoRetur;
use App\Model\OMerchantAdmin;
use App\Model\PurchasingOrderMasuk;
use App\Model\PurchasingOrderDetil;
use App\Model\PurchasingOrderRetur;
use App\Model\User;
use App\Model\Gudang;
use App\Model\PurchasingOrder;
use App\Exports\PO\ReportExport;
use Carbon\Carbon;

use DataTables;
use Session;
use Auth;
use DB;

class OMerchantPoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Builder $htmlBuilder){

        if($request->ajax()){
            $user = User::find(Auth::user()->id);
            $gudang = Gudang::where('user_id', $user->id)->first();
    
            $omerchantpo  = OMerchantPo::with('usaha_o_merchant.usaha')
                ->where('gudang_id', $gudang->id)->get();

            return Datatables::of($omerchantpo)
            ->addColumn('usaha_o_merchant',function($data){
                if(empty($data->usaha_o_merchant->usaha)){
                    return '';
                }
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
                return view('admingudang.purchasingorder._action',[
                    'model' =>$data,
                    'checked_url' =>route('admingudangomerchantpo.verify',['id'=>$data->id,'status'=>'Checked']),
                    'rejected_url' =>route('admingudangomerchantpo.verify',['id'=>$data->id,'status'=>'Rejected']),
                    'detail_url' => route('admingudangomerchantpo.show',$data->id),
                ]);
            })
            ->rawColumns([
                'status', 'action'
            ])
            ->make(true);
        }

        $html=$htmlBuilder
            ->addColumn(['data'=>'nomor_po','name'=>'nomor_po','title'=>'Nomor Purchasing Order'])
            ->addColumn(['data'=>'usaha_o_merchant','name'=>'usaha_o_merchant','title'=>'Usaha'])
            ->addColumn(['data'=>'gudang','name'=>'gudang','title'=>'Gudang'])
            ->addColumn(['data'=>'tanggal','name'=>'tanggal','title'=>'Tanggal Purchasing Order'])
            ->addColumn(['data'=>'total','name'=>'total','title'=>'Total Harga'])
            ->addColumn(['data'=>'status','name'=>'status','title'=>'Status'])
            ->addColumn(['data'=>'action','name'=>'action','title'=>'Action','orderable'=>false,'searchable'=>false]);

        return view('admingudang.masuk.index')->with(compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
    
            $omerchantpo  = OMerchantPoDetail::with('o_merchant_po.usaha_o_merchant.usaha','barang_grosir.varian')
                ->where('o_merchant_po_id',$id)->get();

            return Datatables::of($omerchantpo)
            ->addColumn('o_merchant_po',function($data){    
                return $data->o_merchant_po->usaha_o_merchant->usaha->nama_usaha;
            })
            ->addColumn('barang',function($data){
                return $data->barang_grosir->barang->nama_barang;
            })
            ->addColumn('varian',function($data){
                return $data->barang_grosir->varian->varian_barang . ' ( ' . $data->barang_grosir->harga_jual . '/pcs )';
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
            ->addColumn(['data'=>'varian','name'=>'varian','title'=>'Varian Barang'])
            ->addColumn(['data'=>'jumlah','name'=>'jumlah','title'=>'Jumlah'])
            ->addColumn(['data'=>'harga','name'=>'harga','title'=>'Harga']);

        return view('admingudang.purchasingorder.detail')->with(compact('html'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
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
        return redirect()->route('admingudangomerchantpo.index');
    }
}
