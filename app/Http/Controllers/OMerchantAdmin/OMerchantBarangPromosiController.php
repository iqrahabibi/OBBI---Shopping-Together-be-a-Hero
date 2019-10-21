<?php

namespace App\Http\Controllers\OMerchantAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Html\Builder;
use App\Helper\Formatting;

use App\Model\OMerchantBarangPromosiKategori;
use App\Model\OMerchantBarangPromosi;
use App\Model\OMerchantAdmin;

use DataTables;
use Session;
use DB;
use Auth;

class OMerchantBarangPromosiController extends Controller
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
            $om_barang  = OMerchantBarangPromosi::with('usaha','om_barang_promosi_kategori')->where('kode_usaha',$admin->kode)
            ->get();

            return Datatables::of($om_barang)
            ->addColumn('usaha',function($data){
                return $data->usaha->usaha->nama_usaha;
            })
            ->addColumn('kategori',function($data){
                return $data->om_barang_promosi_kategori->nama_kategori;
            })
            ->addColumn('min_total_harga_pesanan',function($data){
                return Formatting::rupiah($data->min_total_harga_pesanan);
            })
            ->addColumn('jumlah_diskon',function($data){
                return Formatting::rupiah($data->jumlah_diskon);
            })
            ->addColumn('max_jumlah_diskon',function($data){
                return Formatting::rupiah($data->max_jumlah_diskon);
            })
            ->addColumn('action',function($data){
                return view('omerchantadmin.omerchantbarangpromosi._action',[
                    'model' =>$data,
                    'form_url' =>route('omerchantbarangpromosi.destroy',$data->id),
                    'edit_url'=>route('omerchantbarangpromosi.edit',$data->id),
                    'confirm_message'=>'Yakin mau menghapus '.$data->usaha->usaha->nama_usaha.' ?'
                ]);
            })
            ->make(true);
        }

        $html=$htmlBuilder
            ->addColumn(['data'=>'usaha','name'=>'usaha','title'=>'Nama Usaha'])
            ->addColumn(['data'=>'kategori','name'=>'kategori','title'=>'Kategori'])
            ->addColumn(['data'=>'judul','name'=>'judul','title'=>'Judul'])
            ->addColumn(['data'=>'min_total_harga_pesanan','name'=>'min_total_harga_pesanan','title'=>'Min. Total harga Pesanan'])
            ->addColumn(['data'=>'jumlah_diskon','name'=>'jumlah_diskon','title'=>'Jumlah Diskon'])
            ->addColumn(['data'=>'diskon','name'=>'diskon','title'=>'Diskon'])
            ->addColumn(['data'=>'max_jumlah_diskon','name'=>'max_jumlah_diskon','title'=>'Max. Jumlah Diskon'])
            ->addColumn(['data'=>'tanggal_aktif','name'=>'tanggal_aktif','title'=>'Tanggal Aktif'])
            ->addColumn(['data'=>'tanggal_berakhir','name'=>'tanggal_berakhir','title'=>'Tanggal Berakhir'])
            ->addColumn(['data'=>'kelipatan','name'=>'kelipatan','title'=>'Kelipatan'])
            ->addColumn(['data'=>'jam_mulai','name'=>'jam_mulai','title'=>'Jam Mulai'])
            ->addColumn(['data'=>'jam_akhir','name'=>'jam_akhir','title'=>'Jam Akhir'])
            ->addColumn(['data'=>'action','name'=>'action','title'=>'Action','orderable'=>false,'searchable'=>false]);

        return view('omerchantadmin.omerchantbarangpromosi.index')->with(compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('omerchantadmin.omerchantbarangpromosi.create');
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
            'judul'         => 'required|string',
            'om_barang_kategori_id' => 'required|exists:o_merchant_barang_promosi_kategoris,id',
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
            'exists'    => 'Field :attribute tidak ada pada :exists',
        ];

        $this->validate($request, $rules, $messages);

        DB::beginTransaction();

        $kode_usaha = OMerchantAdmin::where('user_id',Auth::user()->id)->first();

        $om_barang_promosi = OMerchantBarangPromosi::create(array_merge($request->all(),[
            "kode_usaha" => $kode_usaha->kode
        ]));

        if($om_barang_promosi->save()){
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

        return redirect()->route('omerchantbarangpromosi.index');

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
        $data   = OMerchantBarangPromosi::with('usaha','om_barang_promosi_kategori')->find($id);

        return view('omerchantadmin.omerchantbarangpromosi.edit',compact('data'));
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
            'judul'         => 'required|string',
            'om_barang_kategori_id' => 'required|exists:o_merchant_barang_promosi_kategoris,id',
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
            'exists'    => 'Field :attribute tidak ada pada :exists',
        ];

        $this->validate($request, $rules, $messages);

        DB::beginTransaction();

        $om_barang_promosi = OMerchantBarangPromosi::findOrFail($id);
        $om_barang_promosi->judul                       = $request->get('judul');
        $om_barang_promosi->min_total_harga_pesanan     = $request->get('min_total_harga_pesanan');
        $om_barang_promosi->jumlah_diskon               = $request->get('jumlah_diskon');
        $om_barang_promosi->diskon                      = $request->get('diskon');
        $om_barang_promosi->max_jumlah_diskon           = $request->get('max_jumlah_diskon');
        $om_barang_promosi->kelipatan                   = $request->get('kelipatan');
        $om_barang_promosi->tanggal_aktif               = $request->get('tanggal_aktif');
        $om_barang_promosi->tanggal_berakhir            = $request->get('tanggal_berakhir');
        $om_barang_promosi->jam_mulai                   = $request->get('jam_mulai');
        $om_barang_promosi->jam_akhir                   = $request->get('jam_akhir');

        if($om_barang_promosi->update()){
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

        return redirect()->route('omerchantbarangpromosi.index');
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

        $om_barang = OMerchantBarangPromosi::find($id);

        if($om_barang->delete()){
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

        return redirect()->route('omerchantbarangpromosi.index');
    }
}
