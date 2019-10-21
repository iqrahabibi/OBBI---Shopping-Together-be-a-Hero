<?php

namespace App\Http\Controllers\HumanResource;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Builder;
use App\Model\Absensi;
use App\Model\Karyawan;
use DataTables;
use Session;

class AbsensiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Builder $htmlBuilder)
    {
        if($request->ajax()){
            $datas = Absensi::with('karyawan')->get();

            return Datatables::of($datas)
            ->addColumn('karyawan',function($data){
                return $data->karyawan->nama_karyawan;
            })
            ->addColumn('action',function($data){
                return view('datatables._action',[
                    'model' =>$data,
                    'form_url' =>route('absensi.destroy',$data->id),
                    'edit_url' =>route('absensi.edit',$data->id),
                    'confirm_message'=>'Yakin mau menghapus data ini ?'
                ]);
            })->make(true);

        }

        $html=$htmlBuilder
            ->addColumn(['data'=>'karyawan','name'=>'karyawan','title'=>'Nama Karyawan'])
            ->addColumn(['data'=>'tanggal_absen','name'=>'tanggal_absen','title'=>'Tanggal Absensi'])
            ->addColumn(['data'=>'absen','name'=>'absen','title'=>'Absen'])
            // ->addColumn(['data'=>'absen_masuk','name'=>'absen_masuk','title'=>'Absen Masuk'])
            // ->addColumn(['data'=>'absen_keluar','name'=>'absen_keluar','title'=>'Absen Keluar'])
            ->addColumn(['data'=>'action','name'=>'action','title'=>'Action','orderable'=>false,'searchable'=>false]);

        return view('administrator.Absensi.index')->with(compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('administrator.Absensi.create');
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
            'karyawan_id'       => 'required|exists:karyawans,id',
            'tanggal_absen'     => 'required|date',
            'absen'             => 'required|string',
            // 'absen_masuk'       => 'required|time',
            // 'absen_keluar'      => 'required|time',

        ];

        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Filed :attribute harus berupa karekter.',
            'exists'    => 'Field :attribute tidak ditemukan.',
        ];

        $this->validate($request, $rules, $messages);

        DB::beginTransaction();

        $data = Absensi::create($request->all());

        if($data->save()){
            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>"Absensi successfully saved."
            ]);
        }else{
            DB::rollBack();

            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be saved."
            ]);
        }
        return redirect()->route('absensi.index');
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
        $data = Absensi::findOrFail($id);
        return view('administrator.Absensi.edit', compact('data'));
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
            'karyawan_id'       => 'required|exists:karyawans,id',
            'tanggal_absen'     => 'required|date',
            'absen'             => 'required|string',
            // 'absen_masuk'       => 'required|time',
            // 'absen_keluar'       => 'required|time',
        ];

        $messages   =   [
            'required' => 'Field :attribute harus diisi.',
            'string'   => 'Field :attribute harus berupa karakter.',
            
        ];

        $this->validate($request, $rules, $messages);

        DB::beginTransaction();

        $data = Absensi::findOrFail($id);
        $data->karyawan_id = $request->get('karyawan_id');
        $data->tanggal_absen = $request->get('tanggal_absen');
        $data->absen = $request->get('absen');
        // $data->absen_masuk = $request->get('absen_masuk');
        // $data->absen_keluar = $request->get('absen_keluar');

        if($data->save()){
            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>"Absensi successfully updated."
            ]);
        }else{
            DB::rollBack();

            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be updated."
            ]);
        }
        return redirect()->route('absensi.index');
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

        $data = Absensi::find($id);
        $nama = $data->nama_karyawan;

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
            "message"=>"Absensi successfully deleted."
        ]);
        return redirect()->route('absensi.index');
    }
}
