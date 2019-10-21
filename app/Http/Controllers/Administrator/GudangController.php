<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Builder;
use App\Model\Gudang;
use App\Model\User;
use App\Model\Role;
use App\Model\BarangStok;
use DataTables;
use Session;

class GudangController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Builder $htmlBuilder)
    {
        if($request->ajax()){
            $datas = Gudang::with('user','kelurahan.kecamatan.kota.provinsi')->get();

            return Datatables::of($datas)
            ->addColumn('user',function($data){
                return optional($data->user)->fullname;
            })
            ->addColumn('kelurahan',function($data){
                if(empty($data->kelurahan)){
                    return '-';
                }
                return $data->kelurahan->kecamatan->kota->provinsi->nama_provinsi . ' - ' .
                    $data->kelurahan->kecamatan->kota->nama_kota . ' - ' .
                    $data->kelurahan->kecamatan->nama_kecamatan . ' - ' .
                    $data->kelurahan->nama_kelurahan;
            })
            ->addColumn('email',function($data){
                return optional($data->user)->email;
            })
            ->addColumn('action',function($data){
                return view('datatables._action',[
                    'model' =>$data,
                    'form_url' =>route('gudang.destroy',$data->id),
                    'edit_url'=>route('gudang.edit',$data->id),
                    'confirm_message'=>'Yakin mau menghapus '.$data->nama_gudang.' ?'
                ]);
            })->make(true);
        }

        $html=$htmlBuilder
            ->addColumn(['data'=>'nama_gudang','name'=>'nama_gudang','title'=>'Nama Gudang'])
            ->addColumn(['data'=>'alamat','name'=>'alamat','title'=>'Alamat'])
            ->addColumn(['data'=>'kelurahan','name'=>'kelurahan','title'=>'Lokasi'])
            ->addColumn(['data'=>'user','name'=>'user','title'=>'User Admin'])
            ->addColumn(['data'=>'email','name'=>'email','title'=>'Email Admin'])
            ->addColumn(['data'=>'action','name'=>'action','title'=>'Action','orderable'=>false,'searchable'=>false]);

        return view('administrator.gudang.index')->with(compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('administrator.gudang.create');
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
            'nama_gudang'   => 'required|string',
            'alamat'        => 'required|string',
            'user_id'       => 'required|exists:users,id',
            'kelurahan_id'  => 'required|exists:kelurahans,id',
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
            'exists'    => 'Field :attribute tidak ditemukan.',
        ];

        $this->validate($request, $rules, $messages);
        
        DB::beginTransaction();

        $data = Gudang::create($request->all());

        /**
         * Add Role Admin Gudang
         * Remove Role Administrator
         */
        $datauser = User::where('id', $data->user_id)->first();
        if(!$datauser->hasRole(Role::find(8)->name)){
            $datauser->roles()->attach(8);
        }
        if($datauser->hasRole(Role::find(2)->name)){
            $datauser->roles()->detach(2);
        }

        if($data->save()){
            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>$request->get('nama_gudang') . " successfully saved."
            ]);
        }else{
            DB::rollBack();

            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be saved."
            ]);
        }
        return redirect()->route('gudang.index');
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
        $data = Gudang::with('kelurahan.kecamatan.kota.provinsi')->findOrFail($id);
        $current = '';
        if(!empty($data->kelurahan)){
            $current = $data->kelurahan->kecamatan->kota->provinsi->nama_provinsi . ' - ' .
                    $data->kelurahan->kecamatan->kota->nama_kota . ' - ' .
                    $data->kelurahan->kecamatan->nama_kecamatan . ' - ' .
                    $data->kelurahan->nama_kelurahan;
        }
        return view('administrator.gudang.edit', compact('data', 'current'));
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
            'nama_gudang'   => 'required|string',
            'alamat'        => 'required|string',
            'user_id'       => 'required|exists:users,id',
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
            'exists'    => 'Field :attribute tidak ditemukan.',
        ];

        $this->validate($request, $rules, $messages);

        if($request->get('current') == ''){
            $rules      =   [ 
                'kelurahan_id'  => 'required|exists:kelurahans,id',
            ];

            $this->validate($request, $rules, $messages);
        }
        
        DB::beginTransaction();
        
        $data = Gudang::findOrFail($id);
        
        /**
         * Remove Last Admin Gudang
         * Add Last Admin as Administrator
         */
        $datauser = User::where('id', $data->user_id)->first();
        if(!empty($datauser) && $datauser->hasRole(Role::find(8)->name)){
            $datauser->roles()->detach(8);
        }
        if(!empty($datauser) && !$datauser->hasRole(Role::find(2)->name)){
            $datauser->roles()->attach(2);
        }

        $data->nama_gudang = $request->get('nama_gudang');
        $data->alamat = $request->get('alamat');
        $data->user_id = $request->get('user_id');
        
        if($request->get('current') == ''){
            $data->kelurahan_id = $request->get('kelurahan_id');
        }

        /**
         * Add Role Admin Gudang
         * Remove Role Administrator
         */
        $datauser = User::where('id', $data->user_id)->first();
        if(!$datauser->hasRole(Role::find(8)->name)){
            $datauser->roles()->attach(8);
        }
        if($datauser->hasRole(Role::find(2)->name)){
            $datauser->roles()->detach(2);
        }

        if($data->update()){
            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>$request->get('nama_gudang') . " successfully updated."
            ]);
        }else{
            DB::rollBack();
            
            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be updated."
            ]);
        }

        return redirect()->route('gudang.index');
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

        $data = Gudang::find($id);
        $nama = $data->nama_gudang;

        // TODO : Check data already used.
        $barangstok = BarangStok::where('gudang_id', $data->id)->count();
        if($barangstok > 0){
            Session::flash("flash_notification",[
                "level"=>"danger",
                "message"=>"Data already used."
            ]);
            return redirect()->back();
        }
        
        /**
         * Remove Last Admin Gudang
         * Add Last Admin as Administrator
         */
        $datauser = User::where('id', $data->user_id)->first();
        if(!empty($datauser) && $datauser->hasRole(Role::find(8)->name)){
            $datauser->roles()->detach(8);
        }
        if(!empty($datauser) && !$datauser->hasRole(Role::find(2)->name)){
            $datauser->roles()->attach(2);
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
        return redirect()->route('gudang.index');
    }
}
