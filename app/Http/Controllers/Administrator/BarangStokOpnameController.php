<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Builder;
use App\Model\BarangStok;
use App\Model\BarangStokOpname;
use App\Model\BarangStokOpnameDetil;
use DataTables;
use Session;

class BarangStokOpnameController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Builder $htmlBuilder)
    {
        if($request->ajax()){
            $datas = BarangStokOpname::with('barang_stok_opname_detil.barang_stok.barang', 'barang_stok_opname_detil.barang_stok.barang_conversi')->get();

            return Datatables::of($datas)
            ->addColumn('type',function($data){
                return $data->barang_stok_opname_detil->type;
            })
            ->addColumn('stok',function($data){
                return $data->barang_stok_opname_detil->barang_stok->barang->nama($data->barang_stok_opname_detil->barang_stok->barang->nama_barang) . ' - ' . 
                    $data->barang_stok_opname_detil->barang_stok->barang_conversi->satuan;
            })
            ->addColumn('jumlah',function($data){
                return $data->barang_stok_opname_detil->jumlah;
            })
            ->addColumn('action',function($data){
                return view('administrator.barangstokopname._action',[
                    'model' =>$data,
                    'form_url' =>route('barangstokopname.destroy',$data->id),
                    'confirm_message'=>'Yakin mau menghapus '.$data->satuan.' ?'
                ]);
            })
            ->make(true);
        }

        $html=$htmlBuilder
            ->addColumn(['data'=>'tanggal','name'=>'tanggal','title'=>'Tanggal'])
            ->addColumn(['data'=>'keterangan','name'=>'keterangan','title'=>'Keterangan'])
            ->addColumn(['data'=>'type','name'=>'type','title'=>'Type'])
            ->addColumn(['data'=>'stok','name'=>'stok','title'=>'Stok'])
            ->addColumn(['data'=>'jumlah','name'=>'jumlah','title'=>'Jumlah'])
            ->addColumn(['data'=>'action','name'=>'action','title'=>'Action','orderable'=>false,'searchable'=>false]);

        return view('administrator.barangstokopname.index')->with(compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('administrator.barangstokopname.create');
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
            'tanggal'           => 'required|date',
            'keterangan'        => 'required|string',
            'type'              => 'required|string',
            'barang_stok_id'    => 'required|exists:barang_stoks,id',
            'jumlah'            => 'required|numeric'
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'date'      => 'Field :attribute harus berupa tanggal.',
            'string'    => 'Field :attribute harus berupa karakter.',
            'exists'    => 'Field :attribute tidak ditemukan.',
        ];

        $this->validate($request, $rules, $messages);
        
        DB::beginTransaction();

        $data = BarangStokOpname::create($request->all());

        $type = $request->get('type');
        $jumlah = $request->get('jumlah');
        $barang_stok_id = $request->get('barang_stok_id');

        $detil = BarangStokOpnameDetil::create([
            'barang_stok_opname_id' => $data->id, 
            'barang_stok_id' => $barang_stok_id,
            'jumlah' => $jumlah,
            'type' => $type
        ]);
            
        $barangstok = BarangStok::find($barang_stok_id);
        if($type == 'Adjustment'){
            $barangstok->jumlah = $barangstok->jumlah + $jumlah;
        }else if($type == 'Write Off'){
            if($barangstok->jumlah < $jumlah){
                DB::rollBack();

                Session::flash("flash_notification",[
                    "level"=>"danger",
                    "message"=>"Data jumlah tidak cukup."
                ]);
                return redirect()->back();
            }

            $barangstok->jumlah = $barangstok->jumlah - $jumlah;
        }
        $barangstok->update();

        if($data->save() && $detil->save()){
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
        return redirect()->route('barangstokopname.index');
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
        return redirect()->route('barangstokopname.index');
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
        return redirect()->route('barangstokopname.index');
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

        $data = BarangStokOpname::find($id);
        $detils = BarangStokOpnameDetil::where('barang_stok_opname_id', $data->id)->get();

        foreach($detils as $detil){
            $barangstok = BarangStok::find($detil->barang_stok_id);
            if($detil->type == 'Adjustment'){
                if($barangstok->jumlah < $detil->jumlah){
                    DB::rollBack();

                    Session::flash("flash_notification",[
                        "level"=>"danger",
                        "message"=>"Data jumlah tidak cukup."
                    ]);
                    return redirect()->back();
                }
                $barangstok->jumlah = $barangstok->jumlah - $detil->jumlah;
            }else if($detil->type == 'Write Off'){
                $barangstok->jumlah = $barangstok->jumlah + $detil->jumlah;
            }
            $barangstok->update();
        }

        if(!$data->delete() || !$detil->delete()) {
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
        return redirect()->route('barangstokopname.index');
    }
}
