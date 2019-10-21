<?php

namespace App\Http\Controllers\OMerchantAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Html\Builder;
use App\Helper\Formatting;

use App\Model\OMerchant;
use App\Model\OMerchantPo;
use App\Model\OMerchantPoDetail;
use App\Model\OMerchantPoMasuk;
use App\Model\OMerchantPoRetur;
use App\Model\OMerchantAdmin;
use App\Model\User;
use App\Model\Gudang;

use App\Exports\UsahaOMerchantPO\ReportExport;
use App\Exports\UsahaOMerchantPO\PoExport;

use Carbon\Carbon;
use Excel;
use DataTables;
use Session;
use Auth;
use DB;

class OMerchantAdminPoController extends Controller
{
    public function index(Request $request, Builder $htmlBuilder){

        if($request->ajax()){
            $user = User::find(Auth::user()->id);
            $om_admin = OMerchantAdmin::where('user_id', $user->id)->first();
    
            $omerchantpo  = OMerchantPo::with('usaha_o_merchant.usaha')
                ->where('kode', $om_admin->kode)->get();

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
                return Formatting::rupiah($data->total);
            })
            ->addColumn('action',function($data){
                return view('omerchantadmin.omerchantadminpo._action_masuk',[
                    'model' => $data,
                    'cetak_url' => route('omerchantadminpo.cetak',$data->id),
                    'pomasuk_url' =>route('omerchantadminpo.masuk',$data->id),
                    'retur'  => route('omerchantadminpo.retur',$data->id),
                    'detail_url' => route('omerchantadminpo.show',$data->id),
                ]);
            })
            ->rawColumns([
                'action'
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

        return view('omerchantadmin.omerchantadminpo.index')->with(compact('html'));
    }

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
                return Formatting::rupiah($data->harga);
            })
            ->make(true);
        }

        $html=$htmlBuilder
            ->addColumn(['data'=>'o_merchant_po','name'=>'o_merchant_po','title'=>'Nama Usaha'])
            ->addColumn(['data'=>'barang','name'=>'barang','title'=>'Nama Barang'])
            ->addColumn(['data'=>'varian','name'=>'varian','title'=>'Varian Barang'])
            ->addColumn(['data'=>'jumlah','name'=>'jumlah','title'=>'Jumlah'])
            ->addColumn(['data'=>'harga','name'=>'harga','title'=>'Harga']);

        return view('omerchantadmin.omerchantadminpo.detail')->with(compact('html'));
    }

    public function pomasuk($id)
    {
        $data = OMerchantPo::with('usaha_o_merchant', 'gudang', 
            'o_merchant_po_detail.barang_grosir.barang', 
            'o_merchant_po_detail.barang_grosir.varian')
            ->findOrFail($id);
        return view('omerchantadmin.omerchantadminpo.masuk', compact('data','id'));
    }

    public function pomasuksave(Request $request, $id)
    {
        $rules      =   [ 
            'tanggal_po_masuk'      => 'required|date',
            'tanggal_batas_retur'   => 'required|date',
            'no_faktur'             => 'required|string'
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'date'      => 'Field :attribute harus berupa tanggal.',
        ];

        $this->validate($request, $rules, $messages);
        
        DB::beginTransaction();

        $om_po = OMerchantPo::findOrFail($id);
        $om_po->tanggal_po_masuk = $request->get('tanggal_po_masuk');
        $om_po->tanggal_batas_retur = $request->get('tanggal_batas_retur');

        $barang_grosirs = $request->get('barang_grosir');
        $varians = $request->get('varian');
        $jumlahs = $request->get('jumlah');
        $hargas = $request->get('harga');

        $jumlah_masuks = $request->get('jumlah_masuk');
        $harga_masuks = $request->get('harga_masuk');
        $jumlah_returs = $request->get('jumlah_retur');

        $total_jumlah_po = 0; $total_harga_po = 0;
        $total_jumlah_masuk = 0; $total_harga_masuk = 0;

        $cek_jumlah = OMerchantPoMasuk::where('o_merchant_po_id',$om_po->id)->sum('jumlah');

        foreach ($jumlah_masuks as $index => $jumlah_masuk) {

            $harga = $hargas[$index];
            $harga = str_replace('Rp', '', $harga);
            $harga = str_replace('.', '', $harga);
                
            $harga_masuk = $harga_masuks[$index];
            $harga_masuk = str_replace('Rp', '', $harga_masuk);
            $harga_masuk = str_replace('.', '', $harga_masuk);

            if(!empty($jumlah_masuks[$index])){
                $po_masuk = OMerchantPoMasuk::create([
                    'o_merchant_po_id' => $om_po->id,
                    'no_faktur' => $request->get('no_faktur'),
                    'barang_grosir_id' => $barang_grosirs[$index],
                    'jumlah' => $jumlah_masuk,
                    'harga' => $harga_masuk
                ]);
            }

            if(!empty($jumlah_returs[$index])){
                OMerchantPoRetur::create([
                    'o_merchant_po_id' => $om_po->id,
                    'no_faktur' => $request->get('no_faktur'),
                    'barang_grosir_id' =>$barang_grosirs[$index],
                    'jumlah' => $jumlah_returs[$index]
                ]);
            }

            $total_jumlah_po += $jumlahs[$index];
            $total_harga_po += $harga;

            $total_jumlah_masuk += $jumlah_masuks[$index];
            $total_harga_masuk += $harga_masuk;
        }

        $total_po_masuk = OMerchantPoMasuk::where('o_merchant_po_id', $om_po->id)->sum('harga');
        $om_po->total_masuk = $total_po_masuk;

        if($om_po->update()){
            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>"Purchasing Order Masuk successfully saved."
            ]);
        }else{
            DB::rollBack();

            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be saved."
            ]);
        }
        return redirect()->route('omerchantadminpo.index');
    }

    public function retur(Request $request, $id, Builder $htmlBuilder){
        if($request->ajax()){
            $datas = OMerchantPoRetur::with('o_merchant_po','barang_grosir')->where('o_merchant_po_id', $id)->get();

            return Datatables::of($datas)
            ->addColumn('o_merchant_po',function($data){
                return $data->o_merchant_po->nomor_po;
            })
            ->addColumn('barang',function($data){
                return $data->barang_grosir->barang->nama($data->barang_grosir->barang->nama_barang);
            })
            ->make(true);
        }

        $html=$htmlBuilder
            ->addColumn(['data'=>'o_merchant_po','name'=>'o_merchant_po','title'=>'Nomor Purchasing Order'])
            ->addColumn(['data'=>'no_faktur','name'=>'no_faktur','title'=>'Nomor Faktur'])
            ->addColumn(['data'=>'barang','name'=>'barang','title'=>'Nama Barang'])
            ->addColumn(['data'=>'jumlah','name'=>'jumlah','title'=>'Jumlah']);

        return view('omerchantadmin.omerchantadminpo.retur')->with(compact('html'));
    }

    public function cetak(Request $request, $id)
    {
        $file = Carbon::now('Asia/Jakarta')->format('YmdHis');
        return Excel::download(
            ReportExport::po($id), $file . '.xlsx'
        );
    }

   
}
