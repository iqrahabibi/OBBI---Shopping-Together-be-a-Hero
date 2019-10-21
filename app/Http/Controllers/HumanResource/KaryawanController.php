<?php

namespace App\Http\Controllers\HumanResource;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Builder;
use App\Model\Karyawan;
use App\Model\Jabatan;
use App\Model\Divisi;
use DataTables;
use Session;

class KaryawanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Builder $htmlBuilder)
    {
        if($request->ajax()){
            $datas = Karyawan::with('divisi', 'jabatan')->get();

            return Datatables::of($datas)
            ->addColumn('divisi',function($data){
                return $data->divisi->nama_divisi;
            })
            ->addColumn('jabatan',function($data){
                return $data->jabatan->nama_jabatan;
            })
            ->addColumn('action',function($data){
                return view('datatables._action',[
                    'model' =>$data,
                    'form_url' =>route('karyawan.destroy',$data->id),
                    'edit_url' =>route('karyawan.edit',$data->id),
                    'confirm_message'=>'Yakin mau menghapus data ini ?'
                ]);
            })->make(true);

        }

        $html=$htmlBuilder
            ->addColumn(['data'=>'divisi','name'=>'divisi','title'=>'Nama Divisi'])
            ->addColumn(['data'=>'jabatan','name'=>'jabatan','title'=>'Nama Jabatan'])
            ->addColumn(['data'=>'nama_karyawan','name'=>'nama_karyawan','title'=>'Nama Karyawan'])
            ->addColumn(['data'=>'alamat','name'=>'alamat','title'=>'Alamat'])
            ->addColumn(['data'=>'tanggal_lahir','name'=>'tanggal_lahir','title'=>'Tanggal Lahir'])
            ->addColumn(['data'=>'tempat_lahir','name'=>'tempat_lahir','title'=>'Tempat Lahir'])
            ->addColumn(['data'=>'handphone1','name'=>'handphone1','title'=>'HandPhone 1'])
            ->addColumn(['data'=>'handphone2','name'=>'handphone2','title'=>'HandPhone 2'])
            ->addColumn(['data'=>'tanggal_masuk','name'=>'tanggal_masuk','title'=>'Tanggal Masuk'])
            ->addColumn(['data'=>'action','name'=>'action','title'=>'Action','orderable'=>false,'searchable'=>false]);

        return view('administrator.Karyawan.index')->with(compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    return view('administrator.Karyawan.create');
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
            'divisi_id'         => 'required|exists:divisis,id',
            'jabatan_id'        => 'required|exists:jabatans,id',
            'nama_karyawan'     => 'required|string',
            'alamat'            => 'required|string',
            'tanggal_lahir'     => 'required|date',
            'tempat_lahir'      => 'required|string',
            'handphone1'        => 'required|numeric',
            'handphone2'        => 'required|numeric',
            'tanggal_masuk'     => 'required|date',

        ];

        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
            'numeric'   => 'Field :attribute harus berupa angka.',
            'exists'    => 'Field :attribute tidak ditemukan.',
        ];

        $this->validate($request, $rules, $messages);

        DB::beginTransaction();

        $data = Karyawan::create($request->all());

        if($data->save()){
            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>$data->nama_karyawan . " successfully saved."
            ]);
        }else{
            DB::rollBack();

            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be saved."
            ]);
        }
        return redirect()->route('karyawan.index');
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
        $data = Karyawan::findOrFail($id);
        return view('administrator.Karyawan.edit', compact('data'));
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
            'divisi_id'         => 'required|exists:divisis,id',
            'jabatan_id'        => 'required|exists:jabatans,id',
            'nama_karyawan'     => 'required|string',
            'alamat'            => 'required|string',
            'tanggal_lahir'     => 'required|date',
            'tempat_lahir'      => 'required|string',
            'handphone1'        => 'required|numeric',
            'handphone2'        => 'required|numeric',
            'tanggal_masuk'     => 'required|date',

        ];

        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
            'numeric'   => 'Field :attribute harus berupa angka.',
            'exists'    => 'Field :attribute tidak ditemukan.',
        ];

        $this->validate($request, $rules, $messages);

        DB::beginTransaction();

        $data = Karyawan::findOrFail($id);
        $data->divisi_id = $request->get('divisi_id');
        $data->jabatan_id = $request->get('jabatan_id');
        $data->nama_karyawan = $request->get('nama_karyawan');
        $data->alamat = $request->get('alamat');
        $data->tanggal_lahir = $request->get('tanggal_lahir');
        $data->tempat_lahir = $request->get('tempat_lahir');
        $data->handphone1 = $request->get('handphone1');
        $data->handphone2 = $request->get('handphone2');
        $data->tanggal_masuk = $request->get('tanggal_masuk');

        if($data->save()){
            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>$data->nama_karyawan . " successfully updated."
            ]);
        }else{
            DB::rollBack();

            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be updated."
            ]);
        }
        return redirect()->route('karyawan.index');

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

        $data = Karyawan::find($id);
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
            "message"=>"$nama successfully deleted."
        ]);
        return redirect()->route('karyawan.index');

    }
}
