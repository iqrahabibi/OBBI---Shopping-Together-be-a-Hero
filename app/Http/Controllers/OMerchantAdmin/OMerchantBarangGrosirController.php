<?php

namespace App\Http\Controllers\OMerchantAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Html\Builder;
use App\Helper\Formatting;

use App\Model\OMerchantBarangGrosir;
use App\Model\OMerchantBarangVarian;
use App\Model\OMerchantAdmin;

use DataTables;
use Session;
use DB;
use Auth;

class OMerchantBarangGrosirController extends Controller
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
            $om_barang  = OMerchantBarangGrosir::with('barang','usaha_om','varian')->where('kode_usaha',$admin->kode)
            ->get();
            
            return Datatables::of($om_barang)
            ->addColumn('barang',function($data){
                return $data->barang->nama_barang;
            })
            ->addColumn('usaha',function($data){
                return $data['usaha_om']['usaha']['nama_usaha'];
            })
            ->addColumn('varian',function($data){
                return $data->varian->varian_barang;
            })
            ->addColumn('harga_jual',function($data){
                return Formatting::rupiah($data->harga_jual);
            })
            ->addColumn('action',function($data){
                return view('omerchantadmin.ombaranggrosir._action',[
                    'model' =>$data,
                    'form_url' =>route('omerchantbaranggrosir.destroy',$data->id),
                    'edit_url'=>route('omerchantbaranggrosir.edit',$data->id),
                    'confirm_message'=>'Yakin mau menghapus data ?'
                ]);
            })
            ->make(true);
        }

        $html=$htmlBuilder
            ->addColumn(['data'=>'barang','name'=>'barang','title'=>'Nama Barang'])
            ->addColumn(['data'=>'usaha','name'=>'usaha','title'=>'Nama Usaha'])
            ->addColumn(['data'=>'varian','name'=>'varian','title'=>'Varian'])
            ->addColumn(['data'=>'qty','name'=>'qty','title'=>'Quantity'])
            ->addColumn(['data'=>'harga_jual','name'=>'harga_jual','title'=>'Harga Jual'])
            ->addColumn(['data'=>'action','name'=>'action','title'=>'Action','orderable'=>false,'searchable'=>false]);

        return view('omerchantadmin.ombaranggrosir.index')->with(compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('omerchantadmin.ombaranggrosir.create');
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
            'varian_id'     => 'required|exists:o_merchant_barang_varians,id',
            'qty'           => 'required|numeric',
            'harga_jual'    => 'required|numeric',
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
            'numeric'   => 'Field :attribute harus berupa angka.',
        ];

        $this->validate($request, $rules, $messages);
        
        DB::beginTransaction();

        $kode_usaha = OMerchantAdmin::where('user_id',Auth::user()->id)->first();

        $om_barang_grosir  = OMerchantBarangGrosir::create(array_merge($request->all(),[
            'kode_usaha' => $kode_usaha['kode']
        ]));
        
        if($om_barang_grosir){
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

        return redirect()->route('omerchantbaranggrosir.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = OMerchantBarangGrosir::with('barang','usaha_om','varian')->findOrFail($id);
        
        return view('omerchantadmin.ombaranggrosir.edit',compact('data'));
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
            'varian_id'     => 'required|exists:o_merchant_barang_varians,id',
            'qty'           => 'required|numeric',
            'harga_jual'    => 'required|numeric',
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
            'numeric'   => 'Field :attribute harus berupa angka.',
        ];

        $this->validate($request, $rules, $messages);
        
        DB::beginTransaction();

        $om_barang_grosir = OMerchantBarangGrosir::with('barang','usaha_om','varian')->findOrFail($id);
        $om_barang_grosir->qty          = $request->get('qty');
        $om_barang_grosir->harga_jual   = $request->get('harga_jual');

        if($om_barang_grosir->save()){
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
        return redirect()->route('omerchantbaranggrosir.index');
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

        $om_barang_grosir = OMerchantBarangGrosir::find($id);

        if($om_barang_grosir->delete()){
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

        return redirect()->route('omerchantbaranggrosir.index');
    }

    public function listombarangvarian($id)
    {
        $result = array(); $list = [];

        $datas = OMerchantBarangVarian::where('barang_id',$id)->get();
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
