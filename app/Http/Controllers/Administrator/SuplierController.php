<?php

namespace App\Http\Controllers\Administrator;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Builder;
use App\Model\Suplier;
use DataTables;
use Session;


class SuplierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Builder $htmlBuilder)
    {
        if($request->ajax()){
            $datas = Suplier::query();

            return Datatables::of($datas)
            ->addColumn('action',function($data){
                return view('datatables._action',[
                    'model' =>$data,
                    'form_url' =>route('suplier.destroy',$data->id),
                    'edit_url' =>route('suplier.edit',$data->id),
                    'confirm_message'=>'Yakin mau menghapus '.$data->nama_suplier.' ?'
                ]);
            })->make(true);
        }

        $html=$htmlBuilder
            ->addColumn(['data'=>'nama_suplier','name'=>'nama_suplier','title'=>'Nama Suplier'])
            ->addColumn(['data'=>'action','name'=>'action','title'=>'Action','orderable'=>false,'searchable'=>false]);

        return view('administrator.suplier.index')->with(compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('administrator.suplier.create');
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
            'nama_suplier' => 'required|string'
        ];

        $messages   = [
            'required' => 'Field :attribute harus diisi.',
            'string'   => 'Field :attribute harus berupa karakter.',
        ];

        $this->validate($request, $rules, $messages);

        DB::beginTransaction();

        $data = Suplier::create($request->all());

        if($data->save()){
            DB:: commit();

            session::flash("flash_notification",[
                "level"=>"success",
                "message"=>$request->get('nama_suplier') . "succesfully saved."
            ]);
        }else{
            DB::rollBack();

            session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be saved."
            ]);
        }
        return redirect()->route('suplier.index');

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
        $data = Suplier::findOrFail($id);
        return view('administrator.suplier.edit', compact('data'));

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
            'nama_suplier' => 'required|string'
        ];

        $messages   =   [
            'required' => 'Field :attribute harus diisi.',
            'string' => 'Field : attribute harus berupa karakter.',
        ];

        $this->validate($request, $rules, $messages);

        DB::beginTransaction();

        $data = Suplier::findOrFail($id);
        $data->nama_suplier = $request->get('nama_suplier');
        
        if($data->update()){
            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>$request->get('nama_suplier') . " succesfully updated."
            ]);
        }else{
            DB::rollBack();

            Session::flash("flash_notification",[
                "level"=>"warning",
                "messages"=>"Data failed to be updated."
            ]);
        }

        return redirect()->route('suplier.index');

        
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

        $data = Suplier::find($id);
        $nama = $data->nama_suplier;

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
        return redirect()->route('suplier.index');
    }
}
