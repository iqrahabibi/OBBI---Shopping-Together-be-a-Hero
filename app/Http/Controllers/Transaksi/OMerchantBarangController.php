<?php

namespace App\Http\Controllers\Transaksi;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Html\Builder;

use App\Model\OMerchantBarang;

use DataTables;
use Session;
use DB;

class OMerchantBarangController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Builder $htmlBuilder)
    {
        if($request->ajax()){
            $om_barang  = OMerchantBarang::with('omerchant','barang','barang_conversi')->get();

            return Datatables::of($om_barang)
            ->addColumn('omerchant',function($data){
                return $data->omerchant['nama_omerchant'];
            })
            ->addColumn('barang',function($data){
                return $data->barang['nama_barang'];
            })
            ->addColumn('barang_conversi',function($data){
                return $data->barang_conversi['satuan'];
            })
            ->addColumn('jumlah',function($data){
                return $data->jumlah;
            })
            ->addColumn('periode',function($data){
                return $data->periode;
            })
            ->addColumn('publish',function($data){
                if($data->publish == 1){
                    return 'Ya';
                }
                return 'Tidak';
            })
            ->addColumn('harga_satuan',function($data){
                return $data->harga_satuan;
            })
            ->addColumn('action',function($data){
                return view('transaksi.om_barang._action',[
                    'model' =>$data,
                    'form_url' =>route('om_barang.destroy',$data->id),
                    'edit_url'=>route('om_barang.edit',$data->id),
                    'status_url' => route('om_barang.status',$data->id),
                    'confirm_message'=>'Yakin mau menghapus '.$data->nama_omerchant.' ?'
                ]);
            })
            ->rawColumns([
                'periode', 'action'
            ])
            ->make(true);
        }

        $html=$htmlBuilder
            ->addColumn(['data'=>'omerchant','name'=>'omerchant','title'=>'Nama O-Merchant'])
            ->addColumn(['data'=>'barang','name'=>'barang','title'=>'Nama Barang'])
            ->addColumn(['data'=>'barang_conversi','name'=>'barang_conversi','title'=>'Barang Konversi'])
            ->addColumn(['data'=>'jumlah','name'=>'jumlah','title'=>'Jumlah'])
            ->addColumn(['data'=>'periode','name'=>'periode','title'=>'Periode'])
            ->addColumn(['data'=>'publish','name'=>'publish','title'=>'Status'])
            ->addColumn(['data'=>'harga_satuan','name'=>'harga_satuan','title'=>'Harga Satuan'])
            ->addColumn(['data'=>'action','name'=>'action','title'=>'Action','orderable'=>false,'searchable'=>false]);

        return view('transaksi.om_barang.index')->with(compact('html'));   
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('transaksi.om_barang.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request);

        $rules      =   [ 
            'kode_omerchant'        => 'required|exists:o_merchants,kode',
            'barang_id'             => 'required|exists:barangs,id',
            'barang_conversi_id'    => 'required|exists:barang_conversis,id',
            'jumlah'                => 'required|numeric',
            'harga_satuan'          => 'required|numeric',
            'periode'               => 'required|date',       
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
            'date'      => 'Field :attribute harus berupa tanggal.',
            'numeric'   => 'Field :attribute harus berupa angka.',
        ];


        $this->validate($request, $rules, $messages);
        
        DB::beginTransaction();

        $om_barang  = OMerchantBarang::create(array_merge($request->all(),[
            'publish' => 1
        ]));
        
        if($om_barang){
            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>"OMerchant Barang successfully saved."
            ]);
        }else{
            DB::rollBack();

            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be saved."
            ]);
        }

        return redirect()->route('om_barang.index');
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
        $om_barang = OMerchantBarang::with('omerchant','barang','barang_conversi')->findOrFail($id);

        return view('transaksi.om_barang.edit',compact('om_barang'));
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
        $rules      =   [ 
            'kode_omerchant'        => 'required|exists:o_merchants,kode',
            'barang_id'             => 'required|exists:barangs,id',
            'barang_conversi_id'    => 'required|exists:barang_conversis,id',
            'jumlah'                => 'required|numeric',
            'harga_satuan'          => 'required|numeric',
            'periode'               => 'required|date',       
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
            'date'      => 'Field :attribute harus berupa tanggal.',
            'numeric'   => 'Field :attribute harus berupa angka.',
        ];


        $this->validate($request, $rules, $messages);
        
        DB::beginTransaction();

        $om_barang = OMerchantBarang::updateOrCreate([
            'id' => $id
        ], $request->all());

        if($om_barang->save()){
            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>"Data successfully saved."
            ]);
        }else{
            DB::rollBack();

            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be saved."
            ]);
        }
        return redirect()->route('om_barang.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        $om_barang = OMerchantBarang::find($id);

        if($om_barang->delete()){
            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>"Data successfully deleted."
            ]);
        }else{
            DB::rollBack();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>"Data failed deleted."
            ]);
        }

        return redirect()->route('om_barang.index');
    }

    public function status($id){
        DB::beginTransaction();

        $om_barang  = OMerchantBarang::findOrFail($id);

        if($om_barang->publish == 1){
            $om_barang->publish = 0;
        }else{
            $om_barang->publish = 1;
        }

        if($om_barang->save()){
            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>"Data successfully changed status."
            ]);
        }else{
            DB::rollBack();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>"Data failed deleted."
            ]);
        }

        return redirect()->route('om_barang.index');
    }
}
