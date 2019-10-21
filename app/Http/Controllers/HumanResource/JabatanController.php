<?php

namespace App\Http\Controllers\HumanResource;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Builder;
use App\Model\Jabatan;
use DataTables;
use Session;

class JabatanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Builder $htmlBuilder)
    {
        if($request->ajax()){
            $datas = Jabatan::query();

            return Datatables::of($datas)
            ->addColumn('action',function($data){
                return view('datatables._action',[
                    'model' =>$data,
                    'form_url' =>route('jabatan.destroy',$data->id),
                    'edit_url' =>route('jabatan.edit',$data->id),
                    'confirm_message'=>'Yakin mau menghapus '.$data->nama_jabatan.' ?' 
                ]);
            })->make(true);
        }

        $html=$htmlBuilder
            ->addColumn(['data'=>'nama_jabatan','name'=>'nama_jabatan','title'=>'Nama Jabatan'])
            ->addColumn(['data'=>'action','name'=>'action','title'=>'Action','orderable'=>false,'searchable'=>false]);

        return view('administrator.Jabatan.index')->with(compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('administrator.Jabatan.create');
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
            'nama_jabatan' => 'required|string'
        ];

        $messages   =   [
            'required' => 'Field :attribute harus diiisi.',
            'string'   => 'Field :attribute harus berupa karakter.',
        ];

        $this->validate($request, $rules, $messages);

        DB::beginTransaction();

        $data = Jabatan::create($request->all());
        
        if($data->save()){
            DB:: commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>$request->get('nama_jabatan') . " succesfully saved."
            ]);
        }else{
            DB::rollBack();

            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be saved."
            ]);
        }
        return redirect()->route('jabatan.index');

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
        $data = Jabatan::findOrFail($id);
        return view('administrator.Jabatan.edit', compact('data'));

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
            'nama_jabatan' => 'required|string'
        ];

        $messages   =   [
            'required' => 'Field :attribute harus diisi.',
            'string' => 'Fied :attribute harus berupa karakter.',
        ];

        $this->validate($request, $rules, $messages);

        DB::beginTransaction();

        $data = Jabatan::findOrFail($id);
        $data->nama_jabatan = $request->get('nama_jabatan');

        if($data->update()){
            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>$request->get('nama_jabatan') . " succesfully updated." 
            ]);
        }else{
            DB::rollBack();

            Session::flash("flash_notification",[
                "level"=>"warning",
                "messages"=>"Data failed to be updated."
            ]);
        }

        return redirect()->route('jabatan.index');


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

        $data = Jabatan::find($id);
        $nama = $data->nama_jabatan;

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
            "message"=>"$nama succesfully deleted."
        ]);
        return redirect()->route('jabatan.index');
    }
}
