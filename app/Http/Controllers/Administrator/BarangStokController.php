<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Builder;
use App\Model\BarangStok;
use DataTables;
use Session;

class BarangStokController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Builder $htmlBuilder)
    {
        if($request->ajax()){
            $datas = BarangStok::with('parent.barang', 'parent.barang_conversi', 'barang', 'barang_conversi', 'gudang')->get();

            return Datatables::of($datas)
            ->addColumn('parent',function($data){
                if($data->parent_id == null){
                    return 'Tidak Ada';
                }
                return $data->parent->barang->nama($data->parent->barang->nama_barang) . ' - ' . 
                    $data->parent->barang_conversi->satuan . ' - ' . $data->parent->jumlah;
            })
            ->addColumn('gudang',function($data){
                return $data->gudang->nama_gudang;
            })
            ->addColumn('stok',function($data){
                return $data->barang->nama($data->barang->nama_barang) . ' - ' . 
                    $data->barang_conversi->satuan . ' - ' . $data->jumlah;
            })
            ->addColumn('publish',function($data){
                if($data->publish == 0){
                    return 'Tidak';
                }
                return 'Ya';
            })
            ->addColumn('action',function($data){
                return view('datatables._action',[
                    'model' =>$data,
                    'form_url' =>route('barangstok.destroy',$data->id),
                    'edit_url'=>route('barangstok.edit',$data->id),
                    'confirm_message'=>'Yakin mau menghapus '.$data->satuan.' ?'
                ]);
            })
            ->make(true);
        }

        $html=$htmlBuilder
            ->addColumn(['data'=>'periode','name'=>'periode','title'=>'Periode'])
            ->addColumn(['data'=>'gudang','name'=>'gudang','title'=>'Gudang'])
            ->addColumn(['data'=>'stok','name'=>'stok','title'=>'Stok'])
            ->addColumn(['data'=>'harga_satuan','name'=>'harga_satuan','title'=>'Harga Satuan'])
            ->addColumn(['data'=>'parent','name'=>'parent','title'=>'Parent'])
            ->addColumn(['data'=>'publish','name'=>'publish','title'=>'Publish'])
            ->addColumn(['data'=>'action','name'=>'action','title'=>'Action','orderable'=>false,'searchable'=>false]);

        return view('administrator.barangstok.index')->with(compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('administrator.barangstok.create');
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
            'barang_id'             => 'required|exists:barangs,id',
            'barang_conversi_id'    => 'required|exists:barang_conversis,id',
            'gudang_id'             => 'required|exists:gudangs,id',
            'jumlah'                => 'required|string',
            'periode'               => 'required|string',
            'publish'               => 'required|string',
            'harga_satuan'          => 'required|numeric'
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
            'exists'    => 'Field :attribute tidak ditemukan.',
        ];

        $this->validate($request, $rules, $messages);
        
        DB::beginTransaction();

        // TODO : Check Data Stok if already exists

        $data = BarangStok::create([
            'parent_id' => null,
            'barang_id' => $request->get('barang_id'),
            'barang_conversi_id' => $request->get('barang_conversi_id'),
            'gudang_id' => $request->get('gudang_id'),
            'jumlah' => $request->get('jumlah'),
            'periode' => $request->get('periode'),
            'publish' => $request->get('publish'),
            'harga_satuan' => $request->get('harga_satuan'),
        ]);

        if($request->get('parent_id')){
            $data->parent_id = $request->get('parent_id');
        }

        if($data->save()){
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
        return redirect()->route('barangstok.index');
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
        $data = BarangStok::findOrFail($id);
        return view('administrator.barangstok.edit', compact('data'));
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
            'barang_id'             => 'required|exists:barangs,id',
            'barang_conversi_id'    => 'required|exists:barang_conversis,id',
            'gudang_id'             => 'required|exists:gudangs,id',
            'jumlah'                => 'required|string',
            'periode'               => 'required|string',
            'publish'               => 'required|string',
            'harga_satuan'          => 'required|numeric'
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
            'exists'    => 'Field :attribute tidak ditemukan.',
        ];

        $this->validate($request, $rules, $messages);
        
        DB::beginTransaction();
        
        $data = BarangStok::findOrFail($id);
        $data->barang_id = $request->get('barang_id');
        $data->barang_conversi_id = $request->get('barang_conversi_id');
        $data->gudang_id = $request->get('gudang_id');
        $data->jumlah = $request->get('jumlah');
        $data->harga_satuan = $request->get('harga_satuan');
        $data->periode = $request->get('periode');
        $data->publish = $request->get('publish');

        if($request->get('parent_id')){
            $data->parent_id = $request->get('parent_id');
        }

        if($data->update()){
            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>"Data successfully updated."
            ]);
        }else{
            DB::rollBack();
            
            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be updated."
            ]);
        }

        return redirect()->route('barangstok.index');
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

        $data = BarangStok::find($id);

        $parent = BarangStok::where('parent_id', $data->id)->count();
        if($parent > 0){
            Session::flash("flash_notification",[
                "level"=>"danger",
                "message"=>"Data already used."
            ]);
            return redirect()->back();
        }

        // TODO : Check data already used

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
            "message"=>"Data successfully deleted."
        ]);
        return redirect()->route('barangstok.index');
    }
}
