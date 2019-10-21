<?php

namespace App\Http\Controllers\AdminGudang;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Builder;
use App\Helper\Formatting;

use App\Model\Barang;
use App\Model\BarangGrosir;
use App\Model\BarangVarian;
use App\Model\Gudang;

use DataTables;
use Session;

class BarangGrosirController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Builder $htmlBuilder)
    {
        $admin_gudang = Gudang::where('user_id',Auth::user()->id)->first();

        if($request->ajax()){
            $datas = BarangGrosir::with('barang','gudang','varian')->where('gudang_id',$admin_gudang->id)->get();

            return Datatables::of($datas)
            ->addColumn('barang',function($data){
                return $data->barang->nama_barang;
            })
            ->addColumn('gudang',function($data){
                return $data->gudang['nama_gudang'];
            })
            ->addColumn('varian',function($data){
                return $data->varian->varian_barang;
            })
            ->addColumn('harga_jual',function($data){
                return Formatting::rupiah($data->harga_jual);
            })
            ->addColumn('action',function($data){
                return view('datatables._action',[
                    'model' =>$data,
                    'form_url' =>route('baranggrosir.destroy',$data->id),
                    'edit_url'=>route('baranggrosir.edit',$data->id),
                    'confirm_message'=>'Yakin mau menghapus '.$data->barang->nama_barang.' ?'
                ]);
            })
            ->make(true);
        }

        $html=$htmlBuilder
            ->addColumn(['data'=>'barang','name'=>'barang','title'=>'Nama Barang'])
            ->addColumn(['data'=>'gudang','name'=>'gudang','title'=>'Nama Gudang'])
            ->addColumn(['data'=>'varian','name'=>'varian','title'=>'Varian'])
            ->addColumn(['data'=>'qty','name'=>'qty','title'=>'Quantity'])
            ->addColumn(['data'=>'harga_jual','name'=>'harga_jual','title'=>'Harga Jual'])
            ->addColumn(['data'=>'action','name'=>'action','title'=>'Action','orderable'=>false,'searchable'=>false]);

        return view('admingudang.baranggrosir.index')->with(compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admingudang.baranggrosir.create');
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
            'barang_id' => 'required|exists:barangs,id',
            'qty'       => 'required|numeric',
            'harga_jual'=> 'required|numeric',
            'varian_id' => 'required|numeric',
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
            'exists'    => 'Field :attribute tidak ditemukan.',
            'numeric'   => 'Field :attribute harus berupa angka.',
        ];

        $this->validate($request, $rules, $messages);

        DB::beginTransaction();

        $data_gudang = Gudang::where('user_id',Auth::user()->id)->first();

        $data = BarangGrosir::create(array_merge($request->all(),[
            'gudang_id' => $data_gudang->id
        ]));

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
        return redirect()->route('baranggrosir.index');
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
        $data = BarangGrosir::with('barang','gudang','varian')->findOrFail($id);
        return view('admingudang.baranggrosir.edit', compact('data'));
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
            'barang_id' => 'required|exists:barangs,id',
            'qty'       => 'required|numeric',
            'harga_jual'=> 'required|numeric',
            'varian_id' => 'required|numeric',
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
            'exists'    => 'Field :attribute tidak ditemukan.',
            'numeric'   => 'Field :attribute harus berupa angka.',
        ];

        $this->validate($request, $rules, $messages);

        DB::beginTransaction();
        $data = BarangGrosir::findOrFail($id);
        $data->qty          = $request->get('qty');
        $data->harga_jual   = $request->get('harga_jual');
        
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

        return redirect()->route('baranggrosir.index');
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

        $data = BarangGrosir::find($id);

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
            "message"=>"successfully deleted."
        ]);
        return redirect()->route('baranggrosir.index');
    }

    public function listbarangvarian($id)
    {
        $result = array(); $list = [];

        $datas = BarangVarian::where('barang_id',$id)->get();
        if($datas){
            foreach ($datas as $data) {
                array_push($list,[
                    'id'=>''.$data->id,
                    'nama'=>$data->varian_barang
                ]);
            }
            $result['hasil'] = $list;
        }else{
            $result['hasil'] = $list;
        }
        return json_encode($result);
    }
}
