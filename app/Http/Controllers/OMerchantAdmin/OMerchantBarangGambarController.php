<?php

namespace App\Http\Controllers\OMerchantAdmin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Html\Builder;
use App\OBBI\obbiHelper;

use App\Model\OMerchantBarangGambar;
use App\Model\OMerchantAdmin;

use DataTables;
use Session;
use DB;
use Auth;

class OMerchantBarangGambarController extends Controller
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
            $om_barang  = OMerchantBarangGambar::with('barang','usaha')->where('kode_usaha',$admin->kode)
            ->get();

            return Datatables::of($om_barang)
            ->addColumn('barang',function($data){
                return $data->barang->nama_barang;
            })
            ->addColumn('usaha',function($data){
                return $data['usaha']['usaha']['nama_usaha'];
            })
            ->addColumn('gambar',function($data){
                return view('omerchantadmin.ombaranggambar._image',[
                    'url' => obbiHelper::storage($data->gambar_barang)
                ]);
            })
            ->addColumn('action',function($data){
                return view('omerchantadmin.ombaranggambar._action',[
                    'model' =>$data,
                    'form_url' =>route('omerchantbaranggambar.destroy',$data->id),
                    'edit_url'=>route('omerchantbaranggambar.edit',$data->id),
                    'confirm_message'=>'Yakin mau menghapus data ?'
                ]);
            })
            ->rawColumns([
                'gambar', 'action'
            ])
            ->make(true);
        }

        $html=$htmlBuilder
            ->addColumn(['data'=>'barang','name'=>'barang','title'=>'Nama Barang'])
            ->addColumn(['data'=>'usaha','name'=>'usaha','title'=>'Nama Usaha'])
            ->addColumn(['data'=>'gambar','name'=>'gambar','title'=>'Barang Gambar'])
            ->addColumn(['data'=>'action','name'=>'action','title'=>'Action','orderable'=>false,'searchable'=>false]);

        return view('omerchantadmin.ombaranggambar.index')->with(compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('omerchantadmin.ombaranggambar.create');
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
            'gambar_barang'         => 'required|mimes:jpg,png,jpeg',       
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
            'mimes'      => 'Field :attribute format tidak sesuai.',
        ];

        $this->validate($request, $rules, $messages);
        
        DB::beginTransaction();

        $kode_usaha = OMerchantAdmin::where('user_id',Auth::user()->id)->first();


        $data = new OMerchantBarangGambar();
        $data->barang_id    = $request->barang_id;
        $data->kode_usaha   = $kode_usaha->kode;

        $file   = $request->file('gambar_barang');

        $extension  = $file->getClientOriginalExtension();
        $fileName   = md5(time()).'.'.$extension;
        $destination = storage_path().DIRECTORY_SEPARATOR.'app/public'.DIRECTORY_SEPARATOR.'omerchant/barang/gambar/';
        $file->move($destination, $fileName);

        $data->gambar_barang   = "/omerchant/barang/gambar/".$fileName;

        if($data->save()){
            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>$request->get('barang_id') . " successfully saved."
            ]);
        }else{
            DB::rollBack();

            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be saved."
            ]);
        }
        return redirect()->route('omerchantbaranggambar.index');
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
        $data = OMerchantBarangGambar::with('barang','usaha')->findOrFail($id);
        $data->gambar_barang = '../../../'.obbiHelper::storage($data->gambar_barang);
        return view('omerchantadmin.ombaranggambar.edit', compact('data'));
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
            'gambar_barang'         => 'required|mimes:jpg,png,jpeg',       
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
            'mimes'      => 'Field :attribute format tidak sesuai.',
        ];

        $this->validate($request, $rules, $messages);
        
        DB::beginTransaction();

        $data = OMerchantBarangGambar::findOrFail($id);

        if(!empty($data->gambar_barang)){
            File::delete('storage'.$data->gambar_barang);
        }

        if($request->has('gambar_barang')){
            
            $file       = $request->file('gambar_barang');

            $extension  = $file->getClientOriginalExtension();
            $fileName   = md5(time()).'.'.$extension;
            $destination = storage_path().DIRECTORY_SEPARATOR.'app/public'.DIRECTORY_SEPARATOR.'omerchant/barang/gambar/';
            $file->move($destination, $fileName);

            $data->gambar_barang    = '/omerchant/barang/gambar/'.$fileName;
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

        return redirect()->route('omerchantbaranggambar.index'); 
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

        $om_barang_gambar = OMerchantBarangGambar::find($id);

        if($om_barang_gambar->delete()){
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

        return redirect()->route('omerchantbaranggambar.index');
    }
}
