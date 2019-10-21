<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Builder;
use App\Model\TargetDonasi;
use App\Model\TipeDonasi;
use App\Model\Saldo;
use DataTables;
use Session;

class TargetDonasiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Builder $htmlBuilder)
    {
        if($request->ajax()){
            $datas = TargetDonasi::with('tipe_donasi', 'agama', 'kelurahan.kecamatan.kota.provinsi')->get();

            return Datatables::of($datas)
            ->addColumn('tipe_donasi',function($data){
                return $data->tipe_donasi->nama_tipe_donasi;
            })
            ->addColumn('agama',function($data){
                return $data->agama->nama_agama;
            })
            ->addColumn('kelurahan',function($data){
                if(empty($data->kelurahan)){
                    return '';
                }
                return $data->kelurahan->kecamatan->kota->provinsi->nama_provinsi . ' - ' .
                $data->kelurahan->kecamatan->kota->nama_kota . ' - ' .
                $data->kelurahan->kecamatan->nama_kecamatan . ' - ' .
                $data->kelurahan->nama_kelurahan;
            })
            ->addColumn('action',function($data){
                return view('datatables._action',[
                    'model' =>$data,
                    'form_url' =>route('targetdonasi.destroy',$data->id),
                    'edit_url'=>route('targetdonasi.edit',$data->id),
                    'confirm_message'=>'Yakin mau menghapus '.$data->nama_targetdonasi.' ?'
                ]);
            })->make(true);
        }

        $html=$htmlBuilder
            ->addColumn(['data'=>'tipe_donasi','name'=>'tipe_donasi','title'=>'Tipe Donasi'])
            ->addColumn(['data'=>'agama','name'=>'agama','title'=>'Target Agama'])
            ->addColumn(['data'=>'nama_target_donasi','name'=>'nama_target_donasi','title'=>'Nama Target Donasi'])
            ->addColumn(['data'=>'kelurahan','name'=>'kelurahan','title'=>'Lokasi'])
            ->addColumn(['data'=>'action','name'=>'action','title'=>'Action','orderable'=>false,'searchable'=>false]);

        return view('administrator.targetdonasi.index')->with(compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('administrator.targetdonasi.create');
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
            'tipe_donasi_id'        => 'required|exists:tipe_donasis,id',
            'agama_id'              => 'required|exists:agamas,id',
            'nama_target_donasi'    => 'required|string',
            'provinsi_id'           => 'required|exists:provinsis,id',
            'kota_id'               => 'required|exists:kotas,id',
            'kecamatan_id'          => 'required|exists:kecamatans,id',
            'kelurahan_id'          => 'required|exists:kelurahans,id',
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
        ];

        $this->validate($request, $rules, $messages);
        
        DB::beginTransaction();

        $data = TargetDonasi::create($request->all());

        if($data->save()){
            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>$request->get('nama_target_donasi') . " successfully saved."
            ]);
        }else{
            DB::rollBack();

            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be saved."
            ]);
        }
        return redirect()->route('targetdonasi.index');
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
        $data = TargetDonasi::with('kelurahan.kecamatan.kota.provinsi')->findOrFail($id);
        $current = '';
        if(!empty($data->kelurahan)){
            $current = $data->kelurahan->kecamatan->kota->provinsi->nama_provinsi . ' - ' .
                    $data->kelurahan->kecamatan->kota->nama_kota . ' - ' .
                    $data->kelurahan->kecamatan->nama_kecamatan . ' - ' .
                    $data->kelurahan->nama_kelurahan;
        }
        return view('administrator.targetdonasi.edit', compact('data', 'current'));
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
            'tipe_donasi_id'        => 'required|exists:tipe_donasis,id',
            'agama_id'              => 'required|exists:agamas,id',
            'nama_target_donasi' => 'required|string'
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
        ];

        $this->validate($request, $rules, $messages);

        if($request->get('current') == ''){
            $rules      =   [ 
                'provinsi_id'   => 'required|exists:provinsis,id',
                'kota_id'       => 'required|exists:kotas,id',
                'kecamatan_id'  => 'required|exists:kecamatans,id',
                'kelurahan_id'  => 'required|exists:kelurahans,id',
            ];

            $this->validate($request, $rules, $messages);
        }
        
        DB::beginTransaction();
        
        $data = TargetDonasi::findOrFail($id);
        $data->tipe_donasi_id = $request->get('tipe_donasi_id');
        $data->agama_id = $request->get('agama_id');
        $data->nama_target_donasi = $request->get('nama_target_donasi');
        
        if($request->get('kelurahan_id')){
            $data->kelurahan_id = $request->get('kelurahan_id');
        }

        if($data->update()){
            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>$request->get('nama_target_donasi') . " successfully updated."
            ]);
        }else{
            DB::rollBack();
            
            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be updated."
            ]);
        }

        return redirect()->route('targetdonasi.index');
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

        $data = TargetDonasi::find($id);
        $nama = $data->nama_target_donasi;

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
        return redirect()->route('targetdonasi.index');
    }
    

    public function list($id)
    {
        $result = array(); $list = [];

        $datas = TargetDonasi::where('kelurahan_id',$id)->get();
        if($datas){
            foreach ($datas as $data) {
                array_push($list,[
                    'id'=>''.$data->id,
                    'nama'=>$data->nama_target_donasi
                ]);
            }
            $result['hasil'] = $list;

            $total = Saldo::with('user.detail')
            ->whereHas('user', function($user) use ($id){
                $user->whereHas('detail', function($detail) use ($id){
                    $detail->where('kelurahan_id', $id);
                });
            })
            ->sum('amal');

            $result['total'] = $total;
        }else{
            $result['hasil'] = $list;
            $result['total'] = 0;
        }
        return json_encode($result);
    }
}
