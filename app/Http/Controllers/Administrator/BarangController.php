<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Builder;
use App\Model\Barang;
use App\Model\Category;
use DataTables;
use Session;

class BarangController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Builder $htmlBuilder)
    {
        if($request->ajax()){
            $datas = Barang::with('category.group')->get();

            return Datatables::of($datas)
            ->addColumn('kategori',function($data){
                return $data->category->group->nama_group . ' - ' . $data->category->nama_kategori;
            })
            ->addColumn('action',function($data){
                return view('datatables._action',[
                    'model' =>$data,
                    'form_url' =>route('barang.destroy',$data->id),
                    'edit_url'=>route('barang.edit',$data->id),
                    'confirm_message'=>'Yakin mau menghapus '.$data->nama_barang.' ?'
                ]);
            })
            ->make(true);
        }

        $html=$htmlBuilder
            ->addColumn(['data'=>'kategori','name'=>'kategori','title'=>'Kategori Barang'])
            ->addColumn(['data'=>'nama_barang','name'=>'nama_barang','title'=>'Nama Barang'])
            ->addColumn(['data'=>'sku','name'=>'sku','title'=>'SKU'])
            ->addColumn(['data'=>'brand','name'=>'brand','title'=>'Brand'])
            ->addColumn(['data'=>'deskripsi','name'=>'deskripsi','title'=>'Deskripsi'])
            ->addColumn(['data'=>'weight','name'=>'weight','title'=>'Berat (gram)'])
            ->addColumn(['data'=>'jumlah_amal','name'=>'jumlah_amal','title'=>'Amal'])
            ->addColumn(['data'=>'keuntungan','name'=>'keuntungan','title'=>'Keuntungan'])
            ->addColumn(['data'=>'action','name'=>'action','title'=>'Action','orderable'=>false,'searchable'=>false]);

        return view('administrator.barang.index')->with(compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('administrator.barang.create');
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
            'category_id'   => 'required|exists:categories,id',
            'nama_barang'   => 'required|string',
            'sku'           => 'numeric',
            'weight'        => 'numeric',
            'jumlah_amal'   => 'required|numeric',
            'keuntungan'    => 'required|numeric',
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
            'exists'    => 'Field :attribute tidak ditemukan.',
            'numeric'   => 'Field :attribute harus berupa angka.',
        ];

        $this->validate($request, $rules, $messages);
        
        DB::beginTransaction();

        $data = Barang::create($request->all());

        if($data->save()){
            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>$request->get('nama_barang') . " successfully saved."
            ]);
        }else{
            DB::rollBack();

            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be saved."
            ]);
        }
        return redirect()->route('barang.index');
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
        $data = Barang::findOrFail($id);
        return view('administrator.barang.edit', compact('data'));
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
            'category_id'   => 'required|exists:categories,id',
            'nama_barang'   => 'required|string',
            'weight'        => 'numeric',
            'jumlah_amal'   => 'required|numeric',
            'keuntungan'    => 'required|numeric',
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
            'exists'    => 'Field :attribute tidak ditemukan.',
        ];

        $this->validate($request, $rules, $messages);
        
        DB::beginTransaction();
        
        $data = Barang::findOrFail($id);
        $data->nama_barang  = $request->get('nama_barang');
        $data->sku          = $request->get('sku');
        $data->brand        = $request->get('brand');
        $data->weight       = $request->get('weight');
        $data->deskripsi    = $request->get('deskripsi');
        $data->jumlah_amal    = $request->get('jumlah_amal');
        $data->keuntungan    = $request->get('keuntungan');
        
        if($data->update()){
            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>$request->get('nama_barang') . " successfully updated."
            ]);
        }else{
            DB::rollBack();
            
            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be updated."
            ]);
        }

        return redirect()->route('barang.index');
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

        $data = Barang::find($id);
        $nama = $data->nama_barang;

        // TODO : Check data already used
        // $kelurahan = Kelurahan::where('kecamatan_id', $data->id)->count();
        // if($kelurahan > 0){
        //     Session::flash("flash_notification",[
        //         "level"=>"danger",
        //         "message"=>"Data already used."
        //     ]);
        //     return redirect()->back();
        // }

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
        return redirect()->route('barang.index');
    }
}
