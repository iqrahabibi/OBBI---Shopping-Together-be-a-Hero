<?php

namespace App\Http\Controllers\AdminGudang;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Builder;
use App\Helper\Formatting;

use App\Model\BarangNasional;
use App\Model\User;
use App\Model\Gudang;

use DataTables;
use Session;
use Auth;

class BarangNasionalController extends Controller
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
            $datas = BarangNasional::with('barang', 'varian', 'gudang')->where('gudang_id',$admin_gudang->id)->get();

            return Datatables::of($datas)
            ->addColumn('barang',function($data){
                return $data->barang->nama_barang;
            })
            ->addColumn('gudang',function($data){
                return $data->gudang->nama_gudang;
            })
            ->addColumn('varian',function($data){
                if(empty($data->varian)){
                    return '-';
                }
                return $data->varian->varian_barang;
            })
            ->addColumn('harga_beli',function($data){
                return Formatting::rupiah($data->harga_beli);
            })
            ->addColumn('harga_jual',function($data){
                return Formatting::rupiah($data->harga_jual);
            })
            ->addColumn('action',function($data){
                return view('datatables._action',[
                    'model' =>$data,
                    'form_url' =>route('barangnasional.destroy',$data->id),
                    'edit_url' =>route('barangnasional.edit',$data->id),
                    'confirm_message'=>'Yakin mau menghapus '.$data->barang->nama_barang.' ?'
                ]);
            })
            ->make(true);
        }

        $html=$htmlBuilder
            ->addColumn(['data'=>'barang','name'=>'barang','title'=>'Barang'])
            ->addColumn(['data'=>'gudang','name'=>'gudang','title'=>'Gudang'])
            ->addColumn(['data'=>'varian','name'=>'varian','title'=>'Varian'])
            ->addColumn(['data'=>'harga_beli','name'=>'harga_beli','title'=>'Harga Beli'])
            ->addColumn(['data'=>'qty','name'=>'qty','title'=>'Quantity'])
            ->addColumn(['data'=>'harga_jual','name'=>'harga_jual','title'=>'Harga Jual'])
            ->addColumn(['data'=>'urut','name'=>'urut','title'=>'Urutan'])
            ->addColumn(['data'=>'action','name'=>'action','title'=>'Action','orderable'=>false,'searchable'=>false]);

        return view('admingudang.barangnasional.index')->with(compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admingudang.barangnasional.create');
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
            'barang_id'     => 'required|exists:barangs,id',
            'qty'           => 'required|string',
            'harga_jual'    => 'required|numeric'
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
            'exists'    => 'Field :attribute tidak ditemukan.',
        ];

        $this->validate($request, $rules, $messages);
        
        DB::beginTransaction();

        $user = User::find(Auth::user()->id);
        $gudang = Gudang::where('user_id', $user->id)->first();

        $data   = BarangNasional::create(array_merge($request->all(),[
            'gudang_id' => $gudang->id
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

        return redirect()->route('barangnasional.index');
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
        $data   = BarangNasional::with('gudang','barang','varian')->findOrFail($id);
        return view('admingudang.barangnasional.edit',compact('data'));
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
            'barang_id'     => 'required|exists:barangs,id',
            'qty'           => 'required|string',
            'harga_jual'    => 'required|numeric'
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
            'exists'    => 'Field :attribute tidak ditemukan.',
        ];

        $this->validate($request, $rules, $messages);
        
        DB::beginTransaction();

        $data   = BarangNasional::findOrFail($id);
        $data->barang_id    = $request->get('barang_id');
        $data->qty          = $request->get('qty');
        $data->harga_jual   = $request->get('harga_jual');
        $data->urut         = $request->get('urut');
        $data->harga_beli   = $request->get('harga_beli');
    
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

        return redirect()->route('barangnasional.index');
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

        $data   = Barangnasional::find($id);

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
        return redirect()->route('barangnasional.index');
    }
}
