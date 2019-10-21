<?php

namespace App\Http\Controllers\OMerchantAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Html\Builder;

use App\Model\OMerchantBarangVarian;
use App\Model\OMerchantAdmin;

use DataTables;
use Session;
use DB;
use Auth;

class OMerchantBarangVarianController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Builder $htmlBuilder)
    {
        $admin  = OMerchantAdmin::where('user_id',Auth::user()->id)->first();

        if($request->ajax()){
            $om_barang  = OMerchantBarangVarian::with('barang','usaha')->where('kode_usaha',$admin->kode)
            ->get();

            return Datatables::of($om_barang)
            ->addColumn('barang',function($data){
                return $data->barang->nama_barang;
            })
            ->addColumn('usaha',function($data){
                return $data->usaha->usaha->nama_usaha;
            })
            ->addColumn('action',function($data){
                return view('omerchantadmin.ombarangvarian._action',[
                    'model' =>$data,
                    'form_url' =>route('omerchantbarangvarian.destroy',$data->id),
                    'edit_url'=>route('omerchantbarangvarian.edit',$data->id),
                    'confirm_message'=>'Yakin mau menghapus '.$data->varian_barang.' ?'
                ]);
            })
            ->make(true);
        }

        $html=$htmlBuilder
            ->addColumn(['data'=>'barang','name'=>'barang','title'=>'Nama Barang'])
            ->addColumn(['data'=>'usaha','name'=>'usaha','title'=>'Nama Usaha'])
            ->addColumn(['data'=>'varian_barang','name'=>'varian_barang','title'=>'Barang Varian'])
            ->addColumn(['data'=>'action','name'=>'action','title'=>'Action','orderable'=>false,'searchable'=>false]);

        return view('omerchantadmin.ombarangvarian.index')->with(compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('omerchantadmin.ombarangvarian.create');
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
            'varian_barang'         => 'required|string',       
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
        ];

        $this->validate($request, $rules, $messages);
        
        DB::beginTransaction();

        $kode_usaha = OMerchantAdmin::where('user_id',Auth::user()->id)->first();

        $om_barang_varian  = OMerchantBarangVarian::create(array_merge($request->all(),[
            'kode_usaha' => $kode_usaha['kode']
        ]));
        
        if($om_barang_varian){
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

        return redirect()->route('omerchantbarangvarian.index');
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
        $om_barang_varian = OMerchantBarangVarian::with('barang','usaha')->findOrFail($id);

        return view('omerchantadmin.ombarangvarian.edit',compact('om_barang_varian'));
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
            'varian_barang'         => 'required|string',       
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
        ];

        $this->validate($request, $rules, $messages);

        DB::beginTransaction();

        $om_barang_varian = OMerchantBarangVarian::with('barang')->findOrFail($id);
        $om_barang_varian->varian_barang = $request->get('varian_barang');

        if($om_barang_varian->save()){
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
        return redirect()->route('omerchantbarangvarian.index');

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

        $om_barang_varian = OMerchantBarangVarian::find($id);

        if($om_barang_varian->delete()){
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

        return redirect()->route('omerchantbarangvarian.index');
    }
}
