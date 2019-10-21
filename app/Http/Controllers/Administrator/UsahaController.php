<?php

namespace App\Http\Controllers\Administrator;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Builder;
use App\Helper\Formatting;

use App\Model\Usaha;

use DataTables;
use Session;

class UsahaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Builder $htmlBuilder)
    {
        if($request->ajax()){
            $datas = Usaha::with('kelurahan.kecamatan.kota.provinsi')->get();

            return Datatables::of($datas)
            ->addColumn('kelurahan',function($data){
                return $data->kelurahan->kecamatan->kota->provinsi->nama_provinsi . ' - ' . 
                    $data->kelurahan->kecamatan->kota->nama_kota . ' - ' .
                    $data->kelurahan->kecamatan->nama_kecamatan . ' - ' . 
                    $data->kelurahan->nama_kelurahan;
            })
            ->addColumn('nama_usaha',function($data){
                return $data->nama_usaha;
            })
            ->addColumn('total',function($data){
                return Formatting::rupiah($data->total_modal);
            })
            ->addColumn('alamat',function($data){
                return $data->alamat;
            })
            ->addColumn('maps',function($data){
                if(empty($data->latitude) || empty($data->longitude)){
                    return 'No location found.';
                }
                return view('administrator.usaha._maps',[
                    'data' =>$data
                ]);
            })
            ->addColumn('action',function($data){
                return view('administrator.usaha._action',[
                    'model' =>$data,
                    'form_url' =>route('usaha.destroy',$data->id),
                    'edit_url' =>route('usaha.edit',$data->id),
                    'ubah_status' =>route('usaha.ubah_status',$data->id),
                    'confirm_message'=>'Yakin mau menghapus '.$data->nama_usaha.' ?'
                ]);
            })
            ->rawColumns([
                'maps', 'action'
            ])->make(true);
        }

        $html=$htmlBuilder
            ->addColumn(['data'=>'nama_usaha','name'=>'nama_usaha','title'=>'Nama Usaha'])
            ->addColumn(['data'=>'kelurahan','name'=>'kelurahan','title'=>'Nama Kelurahan'])
            ->addColumn(['data'=>'deskripsi_usaha','name'=>'deskripsi_usaha','title'=>'Deskripsi'])
            ->addColumn(['data'=>'total','name'=>'total','title'=>'Total'])
            ->addColumn(['data'=>'alamat','name'=>'alamat','title'=>'Alamat'])
            ->addColumn(['data'=>'maps','name'=>'maps','title'=>'Lokasi'])
            ->addColumn(['data'=>'status','name'=>'status','title'=>'Status'])
            ->addColumn(['data'=>'action','name'=>'action','title'=>'Action','orderable'=>false,'searchable'=>false]);

        return view('administrator.usaha.index')->with(compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('administrator.usaha.create');
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
            'provinsi_id'       => 'required|exists:provinsis,id',
            'kota_id'           => 'required|exists:kotas,id',
            'kecamatan_id'      => 'required|exists:kecamatans,id',
            'kelurahan_id'      => 'required|exists:kelurahans,id',
            'nama_usaha'        => 'required|string',
            'deskripsi_usaha'   => 'required|string',
            'alamat'            => 'required|string',
            'total_modal'       => 'required|numeric'
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
            'numeric'   => 'Field :attribute harus berupa angka.',
            'exists'    => 'Field :attribute tidak ditemukan.',
        ];

        $this->validate($request, $rules, $messages);

        DB::beginTransaction();

        $usaha  = Usaha::create(array_merge($request->all(),[
            'status' => 'Open'
        ]));

        if($usaha->save()){
            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>$request->get('nama_usaha') . " successfully saved."
            ]);
        }else{
            DB::rollBack();

            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be saved."
            ]);
        }
        return redirect()->route('usaha.index');
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
        $data  = Usaha::findOrFail($id);

        return view('administrator.usaha.edit',compact('data'));
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
            'nama_usaha'        => 'required|string',
            'deskripsi_usaha'   => 'required|string',
            'alamat'            => 'required|string',
            'total_modal'       => 'required|numeric'
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
            'exists'    => 'Field :attribute tidak ditemukan.',
        ];

        $this->validate($request, $rules, $messages);

        DB::beginTransaction();

        $data   = Usaha::findOrFail($id);
        $data->nama_usaha   = $request->get('nama_usaha');
        $data->deskripsi_usaha   = $request->get('deskripsi_usaha');
        $data->alamat       = $request->get('alamat');
        $data->total_modal  = $request->get('total_modal');
        if($request->get('latitude')){
            $data->latitude  = $request->get('latitude');
        }
        if($request->get('longitude')){
            $data->longitude  = $request->get('longitude');
        }

        if($data->update()){
            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>$request->get('nama_usaha') . " successfully updated."
            ]);
        }else{
            DB::rollBack();
            
            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be updated."
            ]);
        }

        return redirect()->route('usaha.index');
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

        $data = Usaha::find($id);
        $nama = $data->nama_usaha;

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
        return redirect()->route('usaha.index');
    }

    public function ubah_status($id){
        DB::beginTransaction();

        $data   = Usaha::find($id);

        if($data->status == 'Open'){
            $data->status   = 'Closed';
        }else{
            $data->status   = 'Open'; 
        }

        if($data->update()){
            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>"$data->nama_usaha successfully status change."
            ]);
        }else{
            DB::rollBack();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>"failed status change."
            ]);
        }

        return redirect()->route('usaha.index');
    }
}
