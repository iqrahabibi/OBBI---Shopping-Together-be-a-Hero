<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Builder;
use App\Model\Kelurahan;
use App\Model\DetailUser;
use DataTables;
use Session;

class KelurahanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Builder $htmlBuilder)
    {
        if($request->ajax()){
            /**
             *  Allowed showing data when memory exhausted
             *  ini_set('memory_limit', '-1');
             */
            $datas = Kelurahan::with('kecamatan')->skip(0)->limit(10000)->get();
            if ($request->has('page')) {
                $page = $request->get('page') - 1;
                $datas = Kelurahan::with('kecamatan')->skip($page * 10000)->limit(10000)->get();
            }

            return Datatables::of($datas)
            ->addColumn('kecamatan',function($data){
                return $data->kecamatan->nama_kecamatan;
            })
            ->addColumn('action',function($data){
                return view('datatables._action',[
                    'model' =>$data,
                    'form_url' =>route('kelurahan.destroy',$data->id),
                    'edit_url'=>route('kelurahan.edit',$data->id),
                    'confirm_message'=>'Yakin mau menghapus '.$data->nama_kelurahan.' ?'
                ]);
            })
            ->make(true);
        }

        $html=$htmlBuilder
            ->addColumn(['data'=>'kecamatan','name'=>'kecamatan','title'=>'Nama Kecamatan'])
            ->addColumn(['data'=>'nama_kelurahan','name'=>'nama_kelurahan','title'=>'Nama Kelurahan'])
            ->addColumn(['data'=>'action','name'=>'action','title'=>'Action','orderable'=>false,'searchable'=>false]);

        return view('administrator.kelurahan.index')->with(compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = '';
        return view('administrator.kelurahan.create', compact('data'));
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
            'nama_kelurahan'    => 'required|string',
            'kode_pos'          => 'required|string'
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
            'exists'    => 'Field :attribute tidak ditemukan.',
        ];

        $this->validate($request, $rules, $messages);
        
        DB::beginTransaction();

        $data = Kelurahan::create($request->all());

        if($data->save()){
            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>$request->get('nama_kelurahan') . " successfully saved."
            ]);
        }else{
            DB::rollBack();

            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be saved."
            ]);
        }
        return redirect()->route('kelurahan.index');
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
        $data = Kelurahan::findOrFail($id);
        return view('administrator.kelurahan.edit', compact('data'));
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
            'nama_kelurahan'    => 'required|string',
            'kode_pos'          => 'required|string'
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
        ];

        $this->validate($request, $rules, $messages);
        
        DB::beginTransaction();
        
        $data = Kelurahan::findOrFail($id);
        $data->nama_kelurahan = $request->get('nama_kelurahan');
        $data->kode_pos = $request->get('kode_pos');

        if($data->update()){
            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>$request->get('nama_kelurahan') . " successfully updated."
            ]);
        }else{
            DB::rollBack();
            
            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be updated."
            ]);
        }

        return redirect()->route('kelurahan.index');
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

        $data = Kelurahan::find($id);
        $nama = $data->nama_kelurahan;

        $detailuser = DetailUser::where('kelurahan_id', $data->id)->count();
        if($detailuser > 0){
            Session::flash("flash_notification",[
                "level"=>"danger",
                "message"=>"Data already used."
            ]);
            return redirect()->back();
        }

        // TODO : Check License

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
        return redirect()->route('kelurahan.index');
    }

    public function list($id)
    {
        $result = array(); $list = [];

        $datas = Kelurahan::where('kecamatan_id',$id)->get();
        if($datas){
            foreach ($datas as $data) {
                array_push($list,[
                    'id'=>''.$data->id,
                    'nama'=>$data->nama_kelurahan
                ]);
            }
            $result['hasil'] = $list;
        }else{
            $result['hasil'] = $list;
        }
        return json_encode($result);
    }
}
