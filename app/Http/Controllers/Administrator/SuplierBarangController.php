<?php

namespace App\Http\Controllers\Administrator;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Builder;
use App\Helper\Formatting;

use App\Model\SuplierBarang;

use DataTables;
use Session;

class SuplierBarangController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Builder $htmlBuilder)
    {
        if($request->ajax()){
            $datas = SuplierBarang::with('barang','suplier')->get();

            return Datatables::of($datas)
            ->addColumn('barang',function($data){
                return $data->barang->nama_barang;
            })
            ->addColumn('suplier',function($data){
                return $data->suplier->nama_suplier;
            })
            ->addColumn('harga_beli',function($data){
                return Formatting::rupiah($data->harga_beli);
            })
            ->addColumn('action',function($data){
                return view('datatables._action',[
                    'model' =>$data,
                    'form_url' =>route('suplierbarang.destroy',$data->id),
                    'edit_url' =>route('suplierbarang.edit',$data->id),
                    'confirm_message'=>'Yakin mau menghapus '.$data->nama_suplier.' ?'
                ]);
            })->make(true);
        }

        $html=$htmlBuilder
            ->addColumn(['data'=>'barang','name'=>'barang','title'=>'Nama Barang'])
            ->addColumn(['data'=>'suplier','name'=>'suplier','title'=>'Nama Suplier'])
            ->addColumn(['data'=>'harga_beli','name'=>'harga_beli','title'=>'Harga Beli'])
            ->addColumn(['data'=>'urut','name'=>'urut','title'=>'Nomor Urut'])
            ->addColumn(['data'=>'action','name'=>'action','title'=>'Action','orderable'=>false,'searchable'=>false]);

        return view('administrator.suplierbarang.index')->with(compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('administrator.suplierbarang.create');
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
            'barang_id'     => 'required|exists:barangs,id',
            'harga_beli'    => 'required|numeric',
            'urut'          => 'required|numeric'
        ];

        $messages   = [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
            'exists'    => 'Field :attribute harus sesuai dengan :exists .',
            'numeric'   => 'Field :attribute harus berupa angka.'
        ];

        $this->validate($request, $rules, $messages);

        DB::beginTransaction();

        $suplier_barang = SuplierBarang::create($request->all());

        if($suplier_barang->save()){
            DB:: commit();

            session::flash("flash_notification",[
                "level"=>"success",
                "message"=>" Data succesfully saved."
            ]);
        }else{
            DB::rollBack();

            session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be saved."
            ]);
        }

        return redirect()->route('suplierbarang.index');
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
        $data   = SuplierBarang::with('barang','suplier')->find($id);

        return view('administrator.suplierbarang.edit',compact('data'));
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
            'suplier_id'    => 'required|exists:supliers,id',
            'barang_id'     => 'required|exists:barangs,id',
            'harga_beli'    => 'required|numeric',
            'urut'          => 'required|numeric'
        ];

        $messages   = [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
            'exists'    => 'Field :attribute harus sesuai dengan :exists .',
            'numeric'   => 'Field :attribute harus berupa angka.'
        ];

        $this->validate($request, $rules, $messages);

        DB::beginTransaction();

        $suplier_barang = SuplierBarang::findOrFail($id);
        $suplier_barang->harga_beli = $request->get('harga_beli');
        $suplier_barang->urut       = $request->get('urut');

        if($suplier_barang->update()){
            DB:: commit();

            session::flash("flash_notification",[
                "level"=>"success",
                "message"=>" Data succesfully updated."
            ]);
        }else{
            DB::rollBack();

            session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be updated."
            ]);
        }

        return redirect()->route('suplierbarang.index');
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

        $suplier_barang = SuplierBarang::find($id);

        if($suplier_barang->delete()){
            DB:: commit();

            session::flash("flash_notification",[
                "level"=>"success",
                "message"=>" Data succesfully deleted."
            ]);
        }else{
            DB::rollBack();

            session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be deleted."
            ]);
        }

        return redirect()->route('suplierbarang.index');
    }

    public function listbarang($id){
        return SuplierBarang::listbarang($id);
    }
}
