<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Builder;
use App\Model\Gudang;
use App\Model\GudangKurir;
use DataTables;
use Session;

class GudangKurirController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Builder $htmlBuilder)
    {
        if($request->ajax()){
            $datas = GudangKurir::with('gudang')->get();

            return Datatables::of($datas)
            ->addColumn('gudang',function($data){
                return $data->gudang->nama_gudang;
            })
            ->addColumn('action',function($data){
                return view('datatables._action',[
                    'model' =>$data,
                    'form_url' =>route('gudangkurir.destroy',$data->id),
                    'edit_url'=>route('gudangkurir.edit',$data->id),
                    'confirm_message'=>'Yakin mau menghapus '.$data->nama.' ?'
                ]);
            })->make(true);
        }

        $html=$htmlBuilder
            ->addColumn(['data'=>'gudang','name'=>'gudang','title'=>'Nama Gudang'])
            ->addColumn(['data'=>'nama','name'=>'nama','title'=>'Nama Kurir'])
            ->addColumn(['data'=>'action','name'=>'action','title'=>'Action','orderable'=>false,'searchable'=>false]);

        return view('administrator.gudangkurir.index')->with(compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $list_kurir = $this->list_kurir();
        return view('administrator.gudangkurir.create', compact('list_kurir'));
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
            'gudang_id'     => 'required|exists:gudangs,id',
            'nama'          => 'required|string',
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
            'exists'    => 'Field :attribute tidak ditemukan.',
        ];

        $this->validate($request, $rules, $messages);
        
        DB::beginTransaction();

        $data = GudangKurir::create($request->all());

        if($data->save()){
            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>$request->get('nama') . " successfully saved."
            ]);
        }else{
            DB::rollBack();

            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be saved."
            ]);
        }
        return redirect()->route('gudangkurir.index');
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
        $data = GudangKurir::findOrFail($id);
        $list_kurir = $this->list_kurir();
        return view('administrator.gudangkurir.edit', compact('data', 'list_kurir'));
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
            'gudang_id'     => 'required|exists:gudangs,id',
            'nama'          => 'required|string',
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
            'exists'    => 'Field :attribute tidak ditemukan.',
        ];

        $this->validate($request, $rules, $messages);
        
        DB::beginTransaction();
        
        $data = GudangKurir::findOrFail($id);
        $data->gudang_id = $request->get('gudang_id');
        $data->nama = $request->get('nama');

        if($data->update()){
            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>$request->get('nama') . " successfully updated."
            ]);
        }else{
            DB::rollBack();
            
            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be updated."
            ]);
        }

        return redirect()->route('gudangkurir.index');
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

        $data = GudangKurir::find($id);
        $nama = $data->nama;

        if(!$data->delete()) {
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
            "message"=>"$nama successfully deleted."
        ]);
        return redirect()->route('gudangkurir.index');
    }

    private function list_kurir(){
        return [
            'JNE' => 'Jalur Nugraha Eka (JNE)',
            'TIKI' => 'Citra Van Titipan Kilat (TIKI)',
            'POS' => 'POS Indonesia (POS)',
            'PCP' => 'Priority Cargo and Package (PCP)',
            'ESL' => 'Eka Sari Lorena (ESL)',
            'RPX' => 'RPX Holding (RPX)',
            'PANDU' => 'Pandu Logistics (PANDU)',
            'WAHANA' => 'Wahana Prestasi Logistics (WAHANA)',
            'SICEPAT' => 'Sicepat Express (SICEPAT)',
            'J&T' => 'J&T Express (J&T)',
            'PAHALA' => 'Pahala Kencana Express (PAHALA)',
            'SAP' => 'SAP Express (SAP)',
            'JET' => 'JET Express (JET)',
            'SLIS' => 'Solusi Express (SLIS)',
            'EXPEDITO' => 'Expedito (EXPEDITO)',
            'DSE' => '21 Express (DSE)',
            'FIRST' => 'First Logistics (FIRST)',
            'NCS' => 'Nusantara Card Semesta (NCS)',
            'STAR' => 'Star Cargo (STAR)',
            'LION' => 'Lion Parcel (LION)',
            'NINJA' => 'Ninja Xpress (NINJA)',
            'IDL' => 'IDL Cargo (IDL)',
        ];
    }
}
