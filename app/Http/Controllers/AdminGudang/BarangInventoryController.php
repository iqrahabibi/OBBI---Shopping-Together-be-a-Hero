<?php

namespace App\Http\Controllers\AdminGudang;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Builder;

use App\Model\Barang;
use App\Model\BarangInventory;
use App\Model\Gudang;

use DataTables;
use Session;

class BarangInventoryController extends Controller
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
            $datas = BarangInventory::with('barang','gudang')->where('gudang_id',$admin_gudang->id)->get();

            return Datatables::of($datas)
            ->addColumn('barang',function($data){
                return $data->barang->nama_barang;
            })
            ->addColumn('gudang',function($data){
                return $data->gudang['nama_gudang'];
            })
            ->addColumn('action',function($data){
                return view('datatables._action',[
                    'model' =>$data,
                    'form_url' =>route('baranginventory.destroy',$data->id),
                    'edit_url'=>route('baranginventory.edit',$data->id),
                    'confirm_message'=>'Yakin mau menghapus '.$data->barang->nama_barang.' ?'
                ]);
            })
            ->make(true);
        }

        $html=$htmlBuilder
            ->addColumn(['data'=>'barang','name'=>'barang','title'=>'Nama Barang'])
            ->addColumn(['data'=>'gudang','name'=>'gudang','title'=>'Nama Gudang'])
            ->addColumn(['data'=>'qty','name'=>'qty','title'=>'Quantity'])
            ->addColumn(['data'=>'onhold_qty','name'=>'onhold_qty','title'=>'Onhold Quantity'])
            ->addColumn(['data'=>'minimal_qty','name'=>'minimal_qty','title'=>'Minimal Quantity'])
            ->addColumn(['data'=>'action','name'=>'action','title'=>'Action','orderable'=>false,'searchable'=>false]);

        return view('admingudang.baranginventory.index')->with(compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admingudang.baranginventory.create');
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
            'onhold_qty'=> 'required|numeric',
            'minimal_qty'=> 'required|numeric',
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

        $data = BarangInventory::create(array_merge($request->all(),[
            'gudang_id' => $data_gudang->id
        ]));

        if($data->save()){
            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>$request->get('qty') . " successfully saved."
            ]);
        }else{
            DB::rollBack();

            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be saved."
            ]);
        }
        return redirect()->route('baranginventory.index');
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
        $data = BarangInventory::with('barang','gudang')->findOrFail($id);
        return view('admingudang.baranginventory.edit', compact('data'));
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
            'onhold_qty'=> 'required|numeric',
            'minimal_qty'=> 'required|numeric',
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
            'exists'    => 'Field :attribute tidak ditemukan.',
            'numeric'   => 'Field :attribute harus berupa angka.',
        ];

        $this->validate($request, $rules, $messages);
        
        DB::beginTransaction();

        $data = BarangInventory::findOrFail($id);
        $data->qty          = $request->get('qty');
        $data->onhold_qty   = $request->get('onhold_qty');
        $data->minimal_qty  = $request->get('minimal_qty');
        
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

        return redirect()->route('baranginventory.index');
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

        $data = BarangInventory::find($id);

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
        return redirect()->route('baranginventory.index');

    }
}
