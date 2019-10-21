<?php

namespace App\Http\Controllers\Administrator;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Yajra\DataTables\Html\Builder;

use App\Model\Role;
use App\Model\RoleUser;
use App\Model\User;
use App\Model\UserAccessMenu;
use App\Model\Menu;

use DataTables;
use Session;

class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Builder $htmlBuilder)
    {
        if($request->ajax()){
            $datas = Role::all();

            return Datatables::of($datas)
            ->addColumn('action',function($data){
                return view('administrator.role._action',[
                    'model' =>$data,
                    'form_url'  => route('role.destroy',$data->id),
                    'edit_url'  => route('role.edit',$data->id),
                    'menu'      => route('role.menu',$data->id),
                    'confirm_message'=>'Yakin mau menghapus '.$data->rolename.' ?'
                ]);
            })
            ->rawColumns([
                'action'
            ])
            ->make(true);
        }

        $html=$htmlBuilder
            ->addColumn(['data'=>'name','name'=>'name','title'=>'Nama Role'])
            ->addColumn(['data'=>'description','name'=>'description','title'=>'Deskripsi'])
            ->addColumn(['data'=>'action','name'=>'action','title'=>'Action','orderable'=>false,'searchable'=>false]);

        return view('administrator.role.index')->with(compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('administrator.role.create');
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
            'name'          => 'required|string',
            'description'   => 'required|string',
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
        ];

        $this->validate($request, $rules, $messages);
        
        DB::beginTransaction();

        $data = Role::create($request->all());

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
        return redirect()->route('role.index');
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
        $data   = Role::findOrFail($id);
        return view('administrator.role.edit',compact('data'));
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
            'name'          => 'required|string',
            'description'   => 'required|string',
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
        ];

        $this->validate($request, $rules, $messages);
        
        DB::beginTransaction();

        $data = Role::findOrFail($id);

        $data->name = $request->name;
        $data->description = $request->description;

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
        return redirect()->route('role.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        $delete = Role::find($id);

        $cek    = RoleUser::where('role_id',$delete->id)->count();

        if($cek > 0){
            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data masih digunakan."
            ]);
        }else{
            if($delete->delete()){
                DB::commit();
    
                Session::flash("flash_notification",[
                    "level"=>"success",
                    "message"=>"Data successfully deleted."
                ]);
            }else{
                DB::rollBack();
    
                Session::flash("flash_notification",[
                    "level"=>"warning",
                    "message"=>"Data failed to be deleted."
                ]);
            }
        }
        return redirect()->route('role.index');
    }

    public function menu(Request $request, Builder $htmlBuilder, $id){
        if($request->ajax()){
            $datas = UserAccessMenu::with('role','menu')->where('role_id',$id)->get();

            return Datatables::of($datas)
            ->addColumn('menu',function($data){
                return $data->menu->menuname;
            })
            ->addColumn('role',function($data){
                return $data->role->name;
            })
            ->addColumn('action',function($data){
                return view('administrator.role.menu._action',[
                    'model' =>$data,
                    'form_url'  => route('role.menu.destroy',$data->id),
                    'confirm_message'=>'Yakin mau menghapus '.$data->rolename.' ?'
                ]);
            })
            ->rawColumns([
                'action'
            ])
            ->make(true);
        }

        $html=$htmlBuilder
            ->addColumn(['data'=>'menu','name'=>'menu','title'=>'Nama Menu'])
            ->addColumn(['data'=>'role','name'=>'role','title'=>'Nama Role'])
            ->addColumn(['data'=>'action','name'=>'action','title'=>'Action','orderable'=>false,'searchable'=>false]);

        return view('administrator.role.menu.index')->with(compact('html','id'));
    }

    public function menu_create($id){
        return view('administrator.role.menu.create',compact('id'));
    }

    public function menu_store(Request $request){

        $rules      =   [ 
            'menu_id'   => 'required|exists:menus,id',
            'role_id'   => 'required|exists:roles,id',
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'exists'    => 'Field :attribute harus ada pada :exists',
        ];

        $this->validate($request, $rules, $messages);

        DB::beginTransaction();

        $cek_parent = Menu::where('id',$request->menu_id)->first();

        if(!empty($cek_parent)){
            $cek_data_parent = UserAccessMenu::where([
                ['menu_id','=',$cek_parent->parent],
                ['role_id','=',$request->role_id]
            ])->count();

            $array  = array(
                array('role_id' => $request->role_id,'menu_id' => $cek_parent->parent),
                array('role_id' => $request->role_id,'menu_id' => $cek_parent->id)
            );
            if($cek_data_parent == 0){
                $data   = UserAccessMenu::insert($array);

                if($data == true){
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
            }else{
                $data   = new UserAccessMenu();
                $data->menu_id  = $cek_parent->id;
                $data->role_id  = $request->role_id;

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
            }
        }

        return redirect()->route('role.menu',$request->role_id);
    }

    public function menu_destroy($id){
        DB::beginTransaction();

        $delete = UserAccessMenu::find($id);

        if($delete->delete()){
            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>"Data successfully deleted."
            ]);
        }else{
            DB::rollBack();

            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be deleted."
            ]);
        }
        return redirect()->back();
    }
}
