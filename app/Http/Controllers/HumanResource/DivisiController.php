<?php

namespace App\Http\Controllers\HumanResource;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Builder;
use App\Model\Divisi;
use DataTables;
use Session;

class DivisiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Builder $htmlBuilder)
    {
        if($request->ajax()){
            $datas = Divisi::query();

            return Datatables::of($datas)
            ->addColumn('action',function($data){
                return view('datatables._action',[
                    'model' =>$data,
                    'form_url' =>route('divisi.destroy',$data->id),
                    'edit_url' =>route('divisi.edit',$data->id),
                    'confirm_message'=>'Yakin Mau Menghapus '.$data->nama_divisi.' ?'
                ]);
            })->make(true);
        }
        $html=$htmlBuilder
            ->addColumn(['data'=>'nama_divisi','name'=>'nama_divisi','title'=>'Nama Divisi'])
            ->addColumn(['data'=>'action','name'=>'action','title'=>'Action','orderable'=>false,'searchable'=>false]);

        return view('administrator.Divisi.index')->with(compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('administrator.Divisi.create');
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
            'nama_divisi' => 'required|string'
        ];

        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
        ];

        $this->validate($request, $rules, $messages);

        DB::beginTransaction();

        $data = Divisi::create($request->all());

        if($data->save()){
            DB:: commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>$request->get('nama_divisi') . " succesfully saved."
            ]);
        }else{
            DB::rollBack();

            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be saved."
            ]);
        }
        return redirect()->route('divisi.index');
        
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
        $data = divisi::findOrFail($id);
        return view('administrator.Divisi.edit', compact('data'));

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
            'nama_divisi' => 'required|string'
        ];

        $messages   =   [
            'required' => 'Field :attribute harus diisi.',
            'string' => 'Field "attribute harus berupa karakter.',
        ];

        $this->validate($request, $rules, $messages);

        DB::beginTransaction();

        $data = Divisi::findOrFail($id);
        $data->nama_divisi = $request->get('nama_divisi');

        if($data->update()){
            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>$request->get('nama_divisi') . " succesfully updated."

            ]);
        }else{
            DB::rollBack();

            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be updated."
            ]);
        }

        return redirect()->route('divisi.index');

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

        $data = Divisi::find($id);
        $nama = $data->nama_divisi;

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
            "message"=>"$nama succesfull deleted."
        ]);
        return redirect()->route('divisi.index');
    }
}
