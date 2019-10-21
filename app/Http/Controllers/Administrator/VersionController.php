<?php

namespace App\Http\Controllers\Administrator;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Html\Builder;

use App\Model\Version;

use DataTables;
use DB;
use Auth;
use Validator;
use Session;

class VersionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Builder $htmlBuilder)
    {
        if($request->ajax()){
            $datas = Version::query();

            return Datatables::of($datas)
            ->addColumn('action',function($data){
                return view('datatables._action',[
                    'model' =>$data,
                    'form_url' =>route('version.destroy',$data->id),
                    'edit_url'=>route('version.edit',$data->id),
                    'confirm_message'=>'Yakin mau menghapus '.$data->code.' ?'
                ]);
            })->make(true);
        }

        $html=$htmlBuilder
        ->addColumn(['data'=>'code','name'=>'code','title'=>'Kode'])
        ->addColumn(['data'=>'name','name'=>'name','title'=>'Nama'])
        ->addColumn(['data'=>'code_baru','name'=>'code_baru','title'=>'Kode Baru'])
        ->addColumn(['data'=>'wajib','name'=>'version','title'=>'Wajib'])
        ->addColumn(['data'=>'version','name'=>'version','title'=>'Versi'])
        ->addColumn(['data'=>'action','name'=>'action','title'=>'Action','orderable'=>false,'searchable'=>false]);

        return view('administrator.version.index')->with(compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('administrator.version.create');
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
            'code'      => 'required|string',
            'name'      => 'required|string',
            'code_baru' => 'required|string',
            'wajib'     => 'required|numeric:max:1',
            'version'   => 'required|string'
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
            'number'    => 'Field :attribute harus berupa angka.'
        ];

        $this->validate($request, $rules, $messages);
        
        DB::beginTransaction();

        $data = Version::create($request->all());

        if($data->save()){
            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>$request->get('name') . " successfully saved."
            ]);
        }else{
            DB::rollBack();

            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be saved."
            ]);
        }
        return redirect()->route('version.index');
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
        $data = Version::findOrFail($id);
        return view('administrator.version.edit', compact('data'));
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
            'code'      => 'required|string',
            'name'      => 'required|string',
            'code_baru' => 'required|string',
            'wajib'     => 'required|numeric:max:1',
            'version'   => 'required|string'
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
            'number'    => 'Field :attribute harus berupa angka.'
        ];

        $this->validate($request, $rules, $messages);
        
        DB::beginTransaction();
        
        $data = Version::findOrFail($id);
        $data->name = $request->get('name');
        $data->code = $request->get('code');
        $data->code_baru = $request->get('code_baru');
        $data->wajib    = $request->get('wajib');
        $data->version  = $request->get('version');

        if($data->update()){
            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>$request->get('name') . " successfully updated."
            ]);
        }else{
            DB::rollBack();
            
            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be updated."
            ]);
        }

        return redirect()->route('version.index');
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

        $data = Version::find($id);
        $nama = $data->name;

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
        return redirect()->route('version.index');
    }
}
