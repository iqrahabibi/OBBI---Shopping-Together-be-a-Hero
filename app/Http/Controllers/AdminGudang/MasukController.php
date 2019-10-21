<?php

namespace App\Http\Controllers\AdminGudang;

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

class MasukController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Builder $htmlBuilder){
        $user = User::find(Auth::user()->id);
        $gudang = Gudang::where('user_id', $user->id)->first();

        if($request->ajax()){
            $datas = PurchasingOrder::with('user', 'suplier', 'gudang')->where('gudang_id',$gudang->id)->get();

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
                }else if($data->match == 0){
                    return 'Open';
                }    
            })
            ->addColumn('action',function($data){
                if($data->match == 1){
                    return view('admingudang.masuk._actionpomasuk',[
                        'cetak_url' => route('admingudangmasuk.cetak',$data->id),
                    ]);
                }else if($data->match == 0){
                    return view('admingudang.masuk._closed',[
                        'pomasuk_url' =>route('admingudangmasuk.prosespomasuk',$data->id),
                        'closed' => route('admingudangmasuk.closed',$data->id),
                    ]);
                }    
            })
            ->rawColumns([
                'match', 'action'
            ])
            ->make(true);
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
    public function show($id)
    {
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

    public function prosespomasuk($id){
        $data = PurchasingOrder::with('user', 'suplier', 'gudang', 'purchasing_order_detil')
            ->whereHas('purchasing_order_detil', function($detil){
                $detil->with('barang', 'barang_conversi');
            })
            ->findOrFail($id);
        return view('admingudang.masuk.prosesmasuk', compact('data'));
    }

    public function prosespomasuksave(Request $request, $id){
        
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

                $harga = $hargas[$index];
                $harga = str_replace('Rp', '', $harga);
                $harga = str_replace('.', '', $harga);
                
                $harga_masuk = $harga_masuks[$index];
                $harga_masuk = str_replace('Rp', '', $harga_masuk);
                $harga_masuk = str_replace('.', '', $harga_masuk);

                if(!empty($jumlah_masuk)){
                    $po_masuk = PurchasingOrderMasuk::create([
                        'purchasing_order_id' => $po->id,
                        'no_faktur' => $request->get('no_faktur'),
                        'barang_id' => $barangs[$index],
                        'barang_conversi_id' => $barang_conversis[$index],
                        'jumlah' => $jumlah_masuk,
                        'harga' => $harga_masuk
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
                $total_harga_po += $harga;

                $total_jumlah_masuk += $jumlah_masuks[$index];
                $total_harga_masuk += $harga_masuk;
            }
        }

        $total_po_masuk = PurchasingOrderMasuk::where('purchasing_order_id', $po->id)->sum('harga');

        $po->total_masuk = $total_po_masuk;

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
        return redirect()->route('admingudangmasuk.index');
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

        return redirect()->route('admingudangmasuk.index');
    }

    public function cetak(Request $request, $id){
        $file = Carbon::now('Asia/Jakarta')->format('YmdHis');
        return \Excel::download(
            ReportExport::po($id), $file . '.xlsx'
        );
    }
}
