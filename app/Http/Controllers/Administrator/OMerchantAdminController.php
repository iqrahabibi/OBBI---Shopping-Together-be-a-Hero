<?php

namespace App\Http\Controllers\Administrator;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Builder;
use App\Model\OMerchantAdmin;
use App\Model\User;
use App\Model\OMerchant;
use App\Model\Gudang;
use App\Model\Role;
use DataTables;
use Session;

class OMerchantAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Builder $htmlBuilder)
    {
        if($request->ajax()){
            $datas = OMerchantAdmin::with('user', 'usaha_o_merchant.usaha', 'gudang')->get();

            return Datatables::of($datas)
            ->addColumn('user',function($data){
                return $data->user->fullname;
            })
            ->addColumn('level',function($data){
                if($data->level == 1){
                    return 'Kepala';
                }else if($data->level == 2){
                    return 'Karyawan';
                }
                return 'Unknown';
            })
            ->addColumn('email',function($data){
                return $data->user->email;
            })
            ->addColumn('usaha',function($data){
                if(empty($data->usaha_o_merchant->usaha)){
                    return '';
                }
                return $data->usaha_o_merchant->usaha->nama_usaha;
            })
            ->addColumn('gudang',function($data){
                return $data->gudang->nama_gudang;
            })
            ->addColumn('action',function($data){
                return view('datatables._action',[
                    'model' =>$data,
                    'form_url' =>route('omerchantadmin.destroy',$data->id),
                    'edit_url' =>route('omerchantadmin.edit',$data->id),
                    'confirm_message'=>'Yakin mau menghapus data ini ?'
                ]);
            })->make(true);
        }


        $html=$htmlBuilder
             ->addColumn(['data'=>'user','name'=>'user','title'=>'User Admin'])
             ->addColumn(['data'=>'email','name'=>'email','title'=>'Email Admin'])
             ->addColumn(['data'=>'level','name'=>'level','title'=>'Hak Akses'])
             ->addColumn(['data'=>'usaha','name'=>'usaha','title'=>'Usaha OMerchant'])
             ->addColumn(['data'=>'gudang','name'=>'gudang','title'=>'Gudang'])
             ->addColumn(['data'=>'alamat','name'=>'alamat','title'=>'Alamat'])
            //  ->addColumn(['data'=>'usaha','name'=>'usaha','title'=>'usaha'])
            //  ->addColumn(['data'=>'gudang','name'=>'gudang','title'=>'Gudang'])
            //  ->addColumn(['data'=>'alamat','name'=>'alamat','title'=>'Alamat'])
             ->addColumn(['data'=>'action','name'=>'action','title'=>'Action','orderable'=>false,'searchable'=>false]);

        return view('administrator.omerchantadmin.index')->with(compact('html'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('administrator.omerchantadmin.create');
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
            'user_id'       => 'required|exists:users,id',
            'level'         => 'required',
            'kode'          => 'required|exists:usaha_o_merchants,kode',
            'gudang_id'     => 'required|exists:gudangs,id',
            'alamat'        => 'required|string',
        ];

        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
            'exists'    => 'Field :attribute tidak ditemukan.',
        ];

        $this->validate($request, $rules, $messages);

        DB::beginTransaction();

        $data = OMerchantAdmin::create($request->all());

        $datauser = User::where('id', $data->user_id)->first();
        if(!$datauser->hasRole(Role::find(6)->name)){
            $datauser->roles()->attach(6);
        }

        if($data->save()){
            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>"Admin OMerchant successfully saved."
            ]);
        }else{
            DB::rollBack();

            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be saved."
            ]);
        }
        return redirect()->route('omerchantadmin.index');
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
        $data = OMerchantAdmin::findOrFail($id);
        return view('administrator.omerchantadmin.edit', compact('data'));
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
            'user_id'       => 'required|exists:users,id',
            'level'         => 'required',
            'kode'          => 'required|exists:usaha_o_merchants,kode',
            'gudang_id'     => 'required|exists:gudangs,id',
            'alamat'        => 'required|string',
        ];

        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
            'exists'    => 'Field :attribute tidak ditemukan.',
        ];

        $this->validate($request, $rules, $messages);

        DB::beginTransaction();

        $data = OMerchantAdmin::findOrFail($id);
        
        $datauser = User::where('id', $data->user_id)->first();
        if($datauser->hasRole(Role::find(6)->name)){
            $datauser->roles()->detach(6);
        }

        $data->user_id = $request->get('user_id');
        $data->level = $request->get('level');
        $data->kode = $request->get('kode');
        $data->gudang_id = $request->get('gudang_id');
        $data->alamat = $request->get('alamat');
        $data->update();
        
        $datauser = User::where('id', $data->user_id)->first();
        if(!$datauser->hasRole(Role::find(6)->name)){
            $datauser->roles()->attach(6);
        }

        if($data->update()){
            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>"Admin OMerchant successfully updated."
            ]);
        }else{
            DB::rollBack();

            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be updated."
            ]);
        }
        return redirect()->route('omerchantadmin.index');
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

        $data = OMerchantAdmin::find($id);
        
        $datauser = User::where('id', $data->user_id)->first();
        if($datauser->hasRole(Role::find(6)->name)){
            $datauser->roles()->detach(6);
        }

        if(!$data->delete())  {
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
            "message"=>"Data successfully deleted." 
        ]);
        return redirect()->route('omerchantadmin.index');
    }
}
