<?php

namespace App\Http\Controllers\OMerchantAdmin;

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
use App\Model\BarangGrosir;
use App\Model\BarangInventory;

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
    public function index(Request $request, Builder $htmlBuilder)
    {
        $admin  = OMerchantAdmin::where('user_id',Auth::user()->id)->first();

        if($request->ajax()){
            $omerchantpo  = OMerchantPo::with('usaha_o_merchant.usaha', 'gudang')->where('kode',$admin->kode)->get();

            return Datatables::of($omerchantpo)
            ->addColumn('usaha_o_merchant',function($data){
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
                return view('omerchantadmin.omerchantpo._action',[
                    'model' =>$data,
                    'form_url' =>route('omerchantpo.destroy',$data->id),
                    'edit_url'=>route('omerchantpo.edit',$data->id),
                    'detail_url' => route('omerchantpo.show',$data->id),
                    'processed_url' => route('omerchantpo.processed',$data->id),
                    'closed_url' => route('omerchantpo.closed',$data->id),
                    'confirm_message'=>'Yakin mau menghapus '.$data->nama_omerchant.' ?'
                ]);
            })
            ->rawColumns([
                'match', 'action'
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

        return view('omerchantadmin.omerchantpo.index')->with(compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('omerchantadmin.omerchantpo.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules      =   [ 
            'kode'      => 'required|exists:usaha_o_merchants,kode',
            'tanggal'   => 'required|date',
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
            'date'      => 'Field :attribute harus berupa tanggal.',
            'numeric'   => 'Field :attribute harus berupa angka.',
        ];

        $this->validate($request, $rules, $messages);

        $user = User::find(Auth::user()->id);
        $usaha_om = OMerchantAdmin::where('kode', $request->get('kode'))->first();
        if(empty($usaha_om) || $usaha_om->level!=1){
            DB::rollBack();

            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"OMerchant Admin belum ditentukan. Silahkan hubungi Customer Service."
            ]);
            return redirect()->back();
        }
        
        DB::beginTransaction();

        $po = OMerchantPo::create(array_merge($request->all(),[
            'nomor_po' => DATA::autonumber_om_po('o_merchant_pos','nomor_po','OMPO-'),
            'gudang_id' => $usaha_om->gudang_id,
            'match' => 0,
            'total' => 0,
            'status' => 'Requested',
        ]));

        $barangs = $request->get('barang');
        $jumlahs = $request->get('jumlah');
        $hargas = $request->get('harga');

        $total = 0;
        if($barangs[0] != null){
            foreach ($barangs as $index => $barang) {
                if(!empty($barang)){
                    $harga = $hargas[$index];
                    $harga = str_replace('Rp', '', $harga);
                    $harga = str_replace('.', '', $harga);

                    $baranggrosir = BarangGrosir::findOrFail($barang);

                    $jumlah = (int)$jumlahs[$index];

                    if($jumlah % $baranggrosir->qty != 0){
                        DB::rollBack();

                        Session::flash("flash_notification",[
                            "level"=>"danger",
                            "message"=>'Quantity tidak sesuai grosir. ' . 
                                'Jumlah yang tersedia untuk kelipatan ' . $baranggrosir->qty . '. ' . 
                                'Jumlah yang Anda input ' . $jumlah . '. '
                        ]);

                        return redirect()->back();
                    }

                    $inventory = BarangInventory::where('barang_id', $baranggrosir->barang_id)
                        ->where('gudang_id', $baranggrosir->gudang_id)
                        ->first();

                    if($inventory->qty < $jumlah){
                        DB::rollBack();

                        Session::flash("flash_notification",[
                            "level"=>"danger",
                            "message"=>'Quantity yang tersedia tidak cukup sesuai pesanan.'
                        ]);

                        return redirect()->back();
                    }
                    
                    OMerchantPoDetail::create([
                        'o_merchant_po_id' => $po->id,
                        'barang_grosir_id' => $baranggrosir->id,
                        'jumlah' => $jumlah,
                        'harga' => $harga
                    ]);

                    $inventory->qty -= $jumlah;
                    $inventory->update();

                    $total += $harga;
                }
            }
        }

        $po->total = $total;

        if($po->save()){
            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>"Purchasing Order successfully saved."
            ]);
        }else{
            DB::rollBack();

            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be saved."
            ]);
        }
        return redirect()->route('omerchantpo.index');
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

        return view('omerchantadmin.omerchantpo.detail')->with(compact('html'));
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
    public function destroy(Request $request,$id)
    {
        DB::beginTransaction();

        $po = OMerchantPo::find($id);

        if($po->status == 'Processed' || $po->status == 'Closed'){
            DB::rollBack();

            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be deleted. Data already " . $po->status . "."
            ]);
            return redirect()->back();
        }

        $po_detils = OMerchantPoDetail::where('o_merchant_po_id',$po->id)->get();

        foreach($po_detils as $po_detil){

            $baranggrosir = BarangGrosir::findOrFail($po_detil->barang_grosir_id);
            $inventory = BarangInventory::where('barang_id', $baranggrosir->barang_id)
                        ->where('gudang_id', $baranggrosir->gudang_id)
                        ->first();
            $inventory->qty += $po_detil->jumlah;
            $inventory->update();

            if(!$po_detil->delete()){
                DB::rollBack();

                Session::flash("flash_notification",[
                    "level"=>"warning",
                    "message"=>"Data failed to be deleted."
                ]);
                return redirect()->back();
            }
        }

        if(!$po->delete()) {
            DB::rollBack();

            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be deleted."
            ]);
            return redirect()->back();
        }
        if($request->ajax()) return response()->json(['id'=>$id]);

        DB::commit();

        Session::flash("flash_notification",[
            "level"=>"success",
            "message"=>"Data successfully deleted."
        ]);
        return redirect()->route('omerchantpo.index');
    }

    public function processed(Request $request, $id){
        DB::beginTransaction();

        $data = OMerchantPo::find($id);
        $data->status = 'Processed';

        if($data->update()){
            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>"Data successfully processed."
            ]);
        }else{
            DB::rollBack();

            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be processed."
            ]);
        }
        return redirect()->route('omerchantpo.index');
    }

    public function masuk($id){
        $data = OMerchantPo::with('omerchant')
            ->findOrFail($id);

        return view('omerchantadmin.omerchantpo.masuk', compact('data'));
    }

    public function masuksave(Request $request, $id){
        
        if($data->status == 'Closed'){
            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data already closed."
            ]);
            return redirect()->back();
        }

        $rules      =   [ 
            'tanggal_po_masuk'      => 'required|date',
            'tanggal_batas_retur'   => 'required|date'
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'date'      => 'Field :attribute harus berupa tanggal.',
        ];

        $this->validate($request, $rules, $messages);
        
        DB::beginTransaction();

        $po = OMerchantPo::findOrFail($id);
        $po->tanggal_po_masuk = $request->get('tanggal_po_masuk');
        $po->tanggal_batas_retur = $request->get('tanggal_batas_retur');

        $barangs = $request->get('barang');
        $barang_conversis = $request->get('barang_conversi');
        $jumlahs = $request->get('jumlah');
        $hargas = $request->get('harga');

        $jumlah_masuks = $request->get('jumlah_masuk');
        $harga_masuks = $request->get('harga_masuk');

        $total_jumlah_po = 0; $total_harga_po = 0;
        $total_jumlah_masuk = 0; $total_harga_masuk = 0;

        if($jumlah_masuks[0] == null || $harga_masuks[0] == null){
            DB::rollBack();

            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Please input jumlah dan harga yang diterima."
            ]);
            return redirect()->back();
        }else{
            foreach ($jumlah_masuks as $index => $jumlah_masuk) {
                $po_masuk = OMerchantPoMasuk::updateOrCreate([
                    'om_po_id' => $po->id,
                    'barang_stok_id' => $barangs[$index],
                    'barang_conversi_id' => $barang_conversis[$index],
                ],[
                    'jumlah' => $jumlah_masuk,
                    'harga' => $harga_masuks[$index]
                ]);

                $po_detil = OMerchantPoDetail::where('om_po_id', $po->id)
                    ->where('barang_stok_id', $barangs[$index])
                    ->first();
                
                
                if($po_detil->jumlah != $po_masuk->jumlah){
                    OMerchantPoRetur::updateOrCreate([
                        'omerchant_po_masuk_id' => $po_masuk->id
                    ],[
                        'jumlah' => $po_detil->jumlah - $po_masuk->jumlah
                    ]);
                }

                $total_jumlah_po += $jumlahs[$index];
                $total_harga_po += $hargas[$index];

                $total_jumlah_masuk += $jumlah_masuks[$index];
                $total_harga_masuk += $harga_masuks[$index];
            }
        }

        if($total_jumlah_po == $total_jumlah_masuk && 
                $total_harga_po == $total_harga_masuk){
            $po->match = 1;
        }

        $po->total_masuk = $total_harga_po - $total_harga_masuk;

        if($po->update()){
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
        return redirect()->route('omerchantpo.index');
    }

    public function closed(Request $request, $id){
        DB::beginTransaction();

        $data = OMerchantPo::find($id);
        
        if($data->status == 'Closed'){
            DB::rollBack();

            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data already closed."
            ]);
            return redirect()->back();
        }
        if($data->status != 'Processed'){
            DB::rollBack();

            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data is not processed."
            ]);
            return redirect()->back();
        }

        $data->status = 'Closed';

        if($data->update()){
            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>"Data successfully closed."
            ]);
        }else{
            DB::rollBack();

            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be closed."
            ]);
        }
        return redirect()->route('omerchantpo.index');
    }

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
                return $data->total;
            })
            ->addColumn('action',function($data){
                return view('omerchantadmin.omerchantpo._action_monitoring',[
                    'model' =>$data,
                    'detail_url' => route('omerchantpo.monitoringdetil',$data->id),
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

        return view('omerchantadmin.omerchantpo.monitoring')->with(compact('html'));
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
                return $data->harga;
            })
            ->make(true);
        }

        $html=$htmlBuilder
            ->addColumn(['data'=>'o_merchant_po','name'=>'o_merchant_po','title'=>'Nama Usaha'])
            ->addColumn(['data'=>'barang','name'=>'barang','title'=>'Nama Barang'])
            ->addColumn(['data'=>'jumlah','name'=>'jumlah','title'=>'Jumlah'])
            ->addColumn(['data'=>'harga','name'=>'harga','title'=>'Harga']);

        return view('omerchantadmin.omerchantpo.detail')->with(compact('html'));
    }
}
