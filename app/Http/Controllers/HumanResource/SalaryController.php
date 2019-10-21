<?php

namespace App\Http\Controllers\HumanResource;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Builder;
use App\Model\Salary;
use App\Model\Karyawan;
use DataTables;
use Session;

class SalaryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Builder $htmlBuilder)
    {
        if($request->ajax()){
            $datas = Salary::with('karyawan')->get();
            // $datas = Salary::query();

            return Datatables::of($datas)
            ->addColumn('karyawan',function($data){
                return $data->karyawan->nama_karyawan;
            })
            ->addColumn('action',function($data){
                return view('datatables._action',[
                    'model' =>$data,
                    'form_url' =>route('salary.destroy',$data->id),
                    'edit_url' =>route('salary.edit',$data->id),
                    'confirm_message'=>'Yakin mau menghapus data ini ?'
                ]);
            })->make(true);
        
        }

        $html=$htmlBuilder
            ->addColumn(['data'=>'karyawan','name'=>'karyawan','title'=>'Nama Karyawan'])
            ->addColumn(['data'=>'nominal','name'=>'nominal','title'=>'Nominal'])
            ->addColumn(['data'=>'action','name'=>'action','title'=>'Action','orderable'=>false,'searchable'=>false]);

        return view('administrator.Salary.index')->with(compact('html'));

        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('administrator.Salary.create');
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
            'nominal'           => 'required|integer',

        ];

        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
            'exists'    => 'Field :attribute tidak ditemukan.',
        ];

        $this->validate($request, $rules, $messages);

        DB::beginTransaction();

        $data = Salary::create($request->all());

        if($data->save()){
            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>"Salary successflly saved."
            ]);
        }else{
            DB::rollBack();

            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be a saved."
            ]);
        }
        return redirect()->route('salary.index');
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
        $data = Salary::findOrFail($id);
        return view('administrator.Salary.edit', compact('data'));
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
            'karyawan_id'       =>  'required|exists:karyawans,id',
            'nominal'           =>  'required|integer',

        ];

        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
            'exists'    => 'Field :attribute tidak ditemukan.',
        ];

        $this->validate($request, $rules, $messages);

        DB::beginTransaction();

        $data = Salary::findOrFail($id);
        $data->karyawan_id = $request->get('karyawan_id');
        $data->nominal = $request->get('nominal');

        if($data->save()){
            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>"Sallary successfully updated."
            ]);
        }else{
            DB::rollBack();

            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be updated."
            ]);
        }
        return redirect()->route('salary.index');


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

        $data = Salary::find($id);
        $nama = $data->nominal;

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
            "message"=>"Salary successfully deleted."
        ]);
        return redirect()->route('salary.index');
    }
}
