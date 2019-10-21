<?php

namespace App\Http\Controllers\OMerchantAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Html\Builder;
use App\Helper\Formatting;
use App\OBBI\obbiHelper;

use App\Model\OMerchantBarangInventory;
use App\Model\OMerchantAdmin;

use DataTables;
use Session;
use DB;
use Auth;

class OMerchantBarangInventoryController extends Controller
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
            $om_barang  = OMerchantBarangInventory::with('barang','usaha_om')->where('kode_usaha',$admin->kode)
            ->get();
            
            return Datatables::of($om_barang)
            ->addColumn('barang',function($data){
                return $data->barang->nama_barang;
            })
            ->addColumn('usaha',function($data){
                return $data['usaha_om']['usaha']['nama_usaha'];
            })
            ->addColumn('harga',function($data){
                return Formatting::rupiah($data->harga);
            })
            ->addColumn('action',function($data){
                return view('omerchantadmin.ombaranginventory._action',[
                    'model' =>$data,
                    'form_url' =>route('omerchantbaranginventory.destroy',$data->id),
                    'edit_url'=>route('omerchantbaranginventory.edit',$data->id),
                    'confirm_message'=>'Yakin mau menghapus data ?'
                ]);
            })
            ->make(true);
        }

        $html=$htmlBuilder
            ->addColumn(['data'=>'barang','name'=>'barang','title'=>'Nama Barang'])
            ->addColumn(['data'=>'usaha','name'=>'usaha','title'=>'Nama Usaha'])
            ->addColumn(['data'=>'qty','name'=>'qty','title'=>'Quantity'])
            ->addColumn(['data'=>'onhold_qty','name'=>'onhold_qty','title'=>'Onhold Quantity'])
            ->addColumn(['data'=>'minimal_qty','name'=>'minimal_qty','title'=>'Minimal Quantity'])
            ->addColumn(['data'=>'urut','name'=>'urut','title'=>'Urutan'])
            ->addColumn(['data'=>'harga','name'=>'harga','title'=>'Harga'])
            ->addColumn(['data'=>'action','name'=>'action','title'=>'Action','orderable'=>false,'searchable'=>false]);

        return view('omerchantadmin.ombaranginventory.index')->with(compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('omerchantadmin.ombaranginventory.create');
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
            'qty'           => 'required|numeric',
            'onhold_qty'    => 'required|numeric',
            'minimal_qty'   => 'required|numeric',
            'urut'          => 'required|numeric',
            'harga'         => 'required|numeric',
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
            'numeric'   => 'Field :attribute harus berupa angka.',
        ];

        $this->validate($request, $rules, $messages);
        
        DB::beginTransaction();

        $kode_usaha = OMerchantAdmin::where('user_id',Auth::user()->id)->first();

        $om_barang_inventory  = OMerchantBarangInventory::create(array_merge($request->all(),[
            'kode_usaha' => $kode_usaha['kode']
        ]));
        
        if($om_barang_inventory){
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

        return redirect()->route('omerchantbaranginventory.index');
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
        $data = OMerchantBarangInventory::with('barang','usaha_om')->findOrFail($id);
        
        return view('omerchantadmin.ombaranginventory.edit',compact('data'));
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
            'qty'           => 'required|numeric',
            'onhold_qty'    => 'required|numeric',
            'minimal_qty'   => 'required|numeric',
            'urut'          => 'required|numeric',
            'harga'         => 'required|numeric',
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
            'numeric'   => 'Field :attribute harus berupa angka.',
        ];

        $this->validate($request, $rules, $messages);
        
        DB::beginTransaction();

        $om_barang_inventory = OMerchantBarangInventory::with('barang')->findOrFail($id);
        $om_barang_inventory->qty           = $request->get('qty');
        $om_barang_inventory->onhold_qty    = $request->get('onhold_qty');
        $om_barang_inventory->minimal_qty   = $request->get('minimal_qty');
        $om_barang_inventory->urut          = $request->get('urut');
        $om_barang_inventory->harga         = $request->get('harga');

        if($om_barang_inventory->save()){
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
        return redirect()->route('omerchantbaranginventory.index');
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

        $om_barang_inventory = OMerchantBarangInventory::find($id);

        if($om_barang_inventory->delete()){
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

        return redirect()->route('omerchantbaranginventory.index');
    }
}
