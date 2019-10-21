<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Builder;
use App\Helper\Formatting;
use App\OBBI\obbiHelper as DATA;
use App\Model\PurchasingOrder;
use App\Model\PurchasingOrderDetil;
use App\Model\PurchasingOrderMasuk;
use App\Model\PurchasingOrderRetur;
use App\Model\SuplierBarang;
use App\Exports\PO\ReportExport;
use App\Exports\PO\PoExport;
use Carbon\Carbon;
use DataTables;
use Session;
use Excel;

class PurchasingOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Builder $htmlBuilder)
    {
        if($request->ajax()){
            $datas = PurchasingOrder::with('user', 'suplier', 'gudang')->get();

            return Datatables::of($datas)
            ->addColumn('user',function($data){
                return $data->user->fullname;
            })
            ->addColumn('suplier',function($data){
                return $data->suplier->nama_suplier;
            })
            ->addColumn('gudang',function($data){
                return $data->gudang->nama_gudang;
            })
            ->addColumn('total',function($data){
                return Formatting::rupiah($data->total);
            })
            ->addColumn('match',function($data){
                if($data->match == 1){
                    return 'Close';
                }else if(empty($data->tanggal_po_masuk)){
                    return 'Open';
                }else if($data->match == 0){
                    return 'Open';
                }
                return 'Unknown';
            })
            ->addColumn('action',function($data){
                return view('administrator.po._action',[
                    'model' =>$data,
                    'form_url' =>route('po.destroy',$data->id),
                    'confirm_message'=>'Yakin mau menghapus '.$data->nama_agama.' ?',
                    'detail_url'=>route('po.detail',$data->id)
                ]);
            })
            ->rawColumns([
                'match', 'action'
            ])->make(true);
        }

        $html=$htmlBuilder
            ->addColumn(['data'=>'nomor_po','name'=>'nomor_po','title'=>'Nomor Purchasing Order'])
            ->addColumn(['data'=>'tanggal_po','name'=>'tanggal_po','title'=>'Tanggal'])
            ->addColumn(['data'=>'user','name'=>'user','title'=>'User Administrator'])
            ->addColumn(['data'=>'suplier','name'=>'suplier','title'=>'Suplier'])
            ->addColumn(['data'=>'gudang','name'=>'gudang','title'=>'Gudang'])
            ->addColumn(['data'=>'total','name'=>'total','title'=>'Harga Total'])
            ->addColumn(['data'=>'match','name'=>'match','title'=>'Status'])
            ->addColumn(['data'=>'action','name'=>'action','title'=>'Action','orderable'=>false,'searchable'=>false]);

        return view('administrator.po.index')->with(compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('administrator.po.create');
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
            'suplier_id'    => 'required|exists:supliers,id',
            'gudang_id'     => 'required|exists:gudangs,id',
            'tanggal_po'    => 'required|date'
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
            'date'      => 'Field :attribute harus berupa tanggal.',
        ];

        $this->validate($request, $rules, $messages);
        
        DB::beginTransaction();

        $po = PurchasingOrder::create(array_merge($request->all(),[
            'user_id' => Auth::user()->id,
            'nomor_po' => DATA::autonumber_po('purchasing_orders','nomor_po','PO-'),
            'total' => 0,
            'match' => 0
        ]));

        $suplier_barangs = $request->get('suplier_barang');
        $barang_conversis = $request->get('barang_conversi');
        $jumlahs = $request->get('jumlah');
        $hargas = $request->get('harga');


        $total = 0;
        if($suplier_barangs[0] != null){
            foreach ($suplier_barangs as $index => $suplier_barang) {

                $suplier = SuplierBarang::find($suplier_barang);

                if(!empty($suplier)){
                    if(empty($barang_conversis[$index])){
                        Session::flash("flash_notification",[
                            "level"=>"warning",
                            "message"=>"Please fill the form correctly. Barang conversion was not filled."
                        ]);
                        return redirect()->back();
                    }

                    if(empty($jumlahs[$index])){
                        Session::flash("flash_notification",[
                            "level"=>"warning",
                            "message"=>"Please fill the form correctly. Jumlah was not filled."
                        ]);
                        return redirect()->back();
                    }

                    if(empty($hargas[$index])){
                        Session::flash("flash_notification",[
                            "level"=>"warning",
                            "message"=>"Please fill the form correctly. Harga was not filled."
                        ]);
                        return redirect()->back();
                    }

                    if(!empty($suplier)){
                        $harga = $hargas[$index];
                        $harga = str_replace('Rp', '', $harga);
                        $harga = str_replace('.', '', $harga);

                        PurchasingOrderDetil::create([
                            'purchasing_order_id' => $po->id,
                            'barang_id' => $suplier->barang_id,
                            'barang_conversi_id' => $barang_conversis[$index],
                            'jumlah' => $jumlahs[$index],
                            'harga' => $harga
                        ]);

                        $total += $harga;
                    }
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
        return redirect()->route('po.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

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

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();

        $po = PurchasingOrder::find($id);
        $po_detils = PurchasingOrderDetil::where('purchasing_order_id',$po->id)->get();

        // TODO : Check data already used

        foreach($po_detils as $po_detil){
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
        return redirect()->route('po.index');
    }

    public function detail(Request $request, Builder $htmlBuilder, $id)
    {
        if($request->ajax()){
            $datas = PurchasingOrderDetil::with('purchasing_order','barang')->where('purchasing_order_id', $id)->get();

            return Datatables::of($datas)
            ->addColumn('tanggal',function($data){
                return $data->purchasing_order->tanggal;
            })
            ->addColumn('barang',function($data){
                return $data->barang->nama_barang;
            })
            ->addColumn('harga',function($data){
                return Formatting::rupiah($data->harga);
            })
            ->make(true);
        }

        $html=$htmlBuilder
            ->addColumn(['data'=>'barang','name'=>'barang','title'=>'Barang'])
            ->addColumn(['data'=>'jumlah','name'=>'jumlah','title'=>'Jumlah'])
            ->addColumn(['data'=>'harga','name'=>'harga','title'=>'Harga']);

        return view('administrator.po.detail')->with(compact('html'));
    }

    public function pomasuk($id)
    {
        $data = PurchasingOrder::with('user', 'suplier', 'gudang', 'purchasing_order_detil')
            ->whereHas('purchasing_order_detil', function($detil){
                $detil->with('barang', 'barang_conversi');
            })
            ->findOrFail($id);
        return view('administrator.po.masuk', compact('data'));
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

        $po = PurchasingOrder::findOrFail($id);
        $po->tanggal_po_masuk = $request->get('tanggal_po_masuk');
        $po->tanggal_batas_retur = $request->get('tanggal_batas_retur');

        $barangs = $request->get('barang');
        $barang_conversis = $request->get('barang_conversi');
        $jumlahs = $request->get('jumlah');
        $hargas = $request->get('harga');

        $jumlah_masuks = $request->get('jumlah_masuk');
        $harga_masuks = $request->get('harga_masuk');
        $jumlah_retur   = $request->get('jumlah_retur');

        $total_jumlah_po = 0; $total_harga_po = 0;
        $total_jumlah_masuk = 0; $total_harga_masuk = 0;

        if(count($jumlah_masuks) == 0){
            DB::rollBack();

            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Please input jumlah dan harga yang diterima."
            ]);
            return redirect()->back();
        }else{

            foreach ($jumlah_masuks as $index => $jumlah_masuk) {

                if(!empty($jumlah_masuk)){
                    $po_masuk = PurchasingOrderMasuk::create([
                        'purchasing_order_id' => $po->id,
                        'no_faktur' => $request->get('no_faktur'),
                        'barang_id' => $barangs[$index],
                        'barang_conversi_id' => $barang_conversis[$index],
                        'jumlah' => $jumlah_masuk,
                        'harga' => $harga_masuks[$index]
                    ]);
                }

                if(!empty($jumlah_retur[$index])){
                    PurchasingOrderRetur::create([
                        'purchasing_order_id' => $po->id,
                        'no_faktur' => $request->get('no_faktur'),
                        'barang_id' =>$barangs[$index],
                        'jumlah'    => $jumlah_retur[$index]
                    ]);
                }

                $total_jumlah_po += $jumlahs[$index];
                $total_harga_po += $hargas[$index];

                $total_jumlah_masuk += $jumlah_masuks[$index];
                $total_harga_masuk += $harga_masuks[$index];
            }
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
        return redirect()->route('po.index');
    }

    public function retur(Request $request, $id, Builder $htmlBuilder){
        if($request->ajax()){
            $datas = PurchasingOrderRetur::with('purchasing_order')->where('purchasing_order_id', $id)->get();

            return Datatables::of($datas)
            ->addColumn('purchasing_order',function($data){
                return $data->purchasing_order->nomor_po;
            })
            ->addColumn('barang',function($data){
                return $data->barang->nama($data->barang->nama_barang);
            })
            ->make(true);
        }

        $html=$htmlBuilder
            ->addColumn(['data'=>'purchasing_order','name'=>'purchasing_order','title'=>'Nomor PO'])
            ->addColumn(['data'=>'barang','name'=>'barang','title'=>'Nama Barang'])
            ->addColumn(['data'=>'jumlah','name'=>'jumlah','title'=>'Jumlah']);

        return view('administrator.po.retur')->with(compact('html'));
    }

    public function closed(Request $request, $id){
        
        DB::beginTransaction();

        $cek_total_harga_po = PurchasingOrder::where('id',$id)->first();
        $cek_total_harga_po->match = 1;
        
        if($cek_total_harga_po->update()){
            DB::commit();
            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>"Data succcessfully saved."
            ]);
        }else{
            DB::rollBack();
            Session::flash("flash_notification",[
                "level"=>"danger",
                "message"=>"Data failed saved."
            ]);
        }

        return redirect()->route('po.index');

    }

    public function cetak(Request $request, $id){
        $file = Carbon::now('Asia/Jakarta')->format('YmdHis');
        return \Excel::download(
            ReportExport::po($id), $file . '.xlsx'
        );
    }
}
