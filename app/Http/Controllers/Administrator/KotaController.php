<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Builder;
use App\Model\Kota;
use App\Model\Kecamatan;
use DataTables;
use Session;

class KotaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Builder $htmlBuilder)
    {
        if($request->ajax()){
            $datas = Kota::with('provinsi')->get();

            return Datatables::of($datas)
            ->addColumn('provinsi',function($data){
                return $data->provinsi->nama_provinsi;
            })
            ->addColumn('action',function($data){
                return view('datatables._action',[
                    'model' =>$data,
                    'form_url' =>route('kota.destroy',$data->id),
                    'edit_url'=>route('kota.edit',$data->id),
                    'confirm_message'=>'Yakin mau menghapus '.$data->nama_kota.' ?'
                ]);
            })->make(true);
        }

        $html=$htmlBuilder
            // ->addColumn(['data'=>'provinsi','name'=>'provinsi','title'=>'Nama Provinsi'])
            ->addColumn(['data'=>'nama_kota','name'=>'nama_kota','title'=>'Nama Kota'])
            ->addColumn(['data'=>'tipe','name'=>'tipe','title'=>'Tipe'])
            ->addColumn(['data'=>'action','name'=>'action','title'=>'Action','orderable'=>false,'searchable'=>false]);

        return view('administrator.kota.index')->with(compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('administrator.kota.create');
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
            'provinsi_id'   => 'required|exists:provinsis,id',
            'tipe'          => 'required|string',
            'nama_kota'     => 'required|string'
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
            'exists'    => 'Field :attribute tidak ditemukan.',
        ];

        $this->validate($request, $rules, $messages);
        
        DB::beginTransaction();

        $data = Kota::create($request->all());

        if($data->save()){
            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>$request->get('nama_kota') . " successfully saved."
            ]);
        }else{
            DB::rollBack();

            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be saved."
            ]);
        }
        return redirect()->route('kota.index');
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
        $data = Kota::findOrFail($id);
        return view('administrator.kota.edit', compact('data'));
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
            'tipe'          => 'required|string',
            'nama_kota'     => 'required|string'
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
            'exists'    => 'Field :attribute tidak ditemukan.',
        ];

        $this->validate($request, $rules, $messages);
        
        DB::beginTransaction();
        
        $data = Kota::findOrFail($id);
        $data->tipe = $request->get('tipe');
        $data->nama_kota = $request->get('nama_kota');

        if($data->update()){
            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>$request->get('nama_kota') . " successfully updated."
            ]);
        }else{
            DB::rollBack();
            
            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be updated."
            ]);
        }

        return redirect()->route('kota.index');
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

        $data = Kota::find($id);
        $nama = $data->nama_kota;

        $kecamatan = Kecamatan::where('kota_id', $data->id)->count();
        if($kecamatan > 0){
            Session::flash("flash_notification",[
                "level"=>"danger",
                "message"=>"Data already used."
            ]);
            return redirect()->back();
        }

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
        return redirect()->route('kota.index');
    }

    public function list($id)
    {
        $result = array(); $list = [];

        $datas = Kota::where('provinsi_id',$id)->get();
        if($datas){
            foreach ($datas as $data) {
                array_push($list,[
                    'id'=>''.$data->id,
                    'nama'=>$data->nama_kota
                ]);
            }
            $result['hasil'] = $list;
        }else{
            $result['hasil'] = $list;
        }
        return json_encode($result);
    }
}
