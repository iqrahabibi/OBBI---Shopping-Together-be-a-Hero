<?php

namespace App\Http\Controllers\OMerchantAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Html\Builder;

use App\Model\OMerchantBarangPromosiKategori;
use App\Model\OMerchantAdmin;

use DataTables;
use Session;
use DB;
use Auth;

class OMerchantBarangPromosiKategoriController extends Controller
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
            $om_barang  = OMerchantBarangPromosiKategori::with('usaha')->where('kode_usaha',$admin->kode)
            ->get();

            return Datatables::of($om_barang)
            ->addColumn('usaha',function($data){
                return $data->usaha->usaha->nama_usaha;
            })
            ->addColumn('action',function($data){
                return view('omerchantadmin.omerchantbarangpromosikategori._action',[
                    'model' =>$data,
                    'form_url' =>route('omerchantbarangpromosikategori.destroy',$data->id),
                    'edit_url'=>route('omerchantbarangpromosikategori.edit',$data->id),
                    'show_promosi' => route('omerchantbarangpromosikategori.show',$data->id),
                    'confirm_message'=>'Yakin mau menghapus '.$data->usaha->usaha->nama_usaha.' ?'
                ]);
            })
            ->make(true);
        }

        $html=$htmlBuilder
            ->addColumn(['data'=>'usaha','name'=>'usaha','title'=>'Nama Usaha'])
            ->addColumn(['data'=>'nama_kategori','name'=>'nama_kategori','title'=>'Nama Kategori'])
            ->addColumn(['data'=>'deskripsi','name'=>'deskripsi','title'=>'Deskripsi'])
            ->addColumn(['data'=>'action','name'=>'action','title'=>'Action','orderable'=>false,'searchable'=>false]);

        return view('omerchantadmin.omerchantbarangpromosikategori.index')->with(compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('omerchantadmin.omerchantbarangpromosikategori.create');
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
            'kode_usaha'    => 'required|exists:usaha_o_merchants,kode',
            'nama_kategori' => 'required',
            'deskripsi'     => 'required',
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
            'exists'    => 'Field :attribute tidak ada pada :exists',
        ];

        $this->validate($request, $rules, $messages);

        DB::beginTransaction();

        $kode_usaha = OMerchantAdmin::where('user_id',Auth::user()->id)->first();

        $om_barang_promosi_kategori = OMerchantBarangPromosiKategori::create(array_merge($request->all(),[
            "kode_usaha" => $kode_usaha->kode
        ]));

        if($om_barang_promosi_kategori->save()){
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

        return redirect()->route('omerchantbarangpromosikategori.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if($id == 1){
            return redirect()->route('omerchantbarangpromosi.index');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data   = OMerchantBarangPromosikategori::with('usaha')->find($id);
        return view('omerchantadmin.omerchantbarangpromosikategori.edit',compact('data'));
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
            'kode_usaha'    => 'required|exists:usaha_o_merchants,kode',
            'nama_kategori' => 'required',
            'deskripsi'     => 'required',
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
        ];

        $this->validate($request, $rules, $messages);

        DB::beginTransaction();


        $om_barang_promosi_kategori = OMerchantBarangPromosiKategori::findOrFail($id);
        $om_barang_promosi_kategori->nama_kategori  = $request->get('nama_kategori');
        $om_barang_promosi_kategori->deskripsi      = $request->get('deskripsi');

        if($om_barang_promosi_kategori->update()){
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

        return redirect()->route('omerchantbarangpromosikategori.index');
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

        $om_barang_inventory = OMerchantBarangPromosiKategori::find($id);

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

        return redirect()->route('omerchantbarangpromosikategori.index');
    }
}
