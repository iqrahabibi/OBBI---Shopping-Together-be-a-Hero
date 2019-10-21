<?php

namespace App\Http\Controllers\Administrator;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Yajra\DataTables\Html\Builder;

use App\Model\Menu;
use App\Model\UserAccessMenu;
use App\Model\User;

use DataTables;
use Session;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Builder $htmlBuilder)
    {
        if($request->ajax()){
            $datas = Menu::all();

            return Datatables::of($datas)
            ->addColumn('icons',function($data){
                return view('administrator.menu._icon',[
                    'model' => $data,
                    'icon'  => $data->icon
                ]);
            })
            ->addColumn('action',function($data){
                return view('administrator.menu._action',[
                    'model' =>$data,
                    'form_url'  =>route('menu.destroy',$data->id),
                    'edit_url'  =>route('menu.edit',$data->id),
                    'confirm_message'=>'Yakin mau menghapus '.$data->menuname.' ?'
                ]);
            })
            ->rawColumns([
                'action'
            ])
            ->make(true);
        }

        $html=$htmlBuilder
            ->addColumn(['data'=>'menuname','name'=>'menuname','title'=>'Nama Menu'])
            ->addColumn(['data'=>'url','name'=>'url','title'=>'URL'])
            ->addColumn(['data'=>'parent','name'=>'parent','title'=>'Parent'])
            ->addColumn(['data'=>'level','name'=>'level','title'=>'Level'])
            ->addColumn(['data'=>'icons','name'=>'icons','title'=>'File icon'])
            ->addColumn(['data'=>'resource','name'=>'resource','title'=>'Resource'])
            ->addColumn(['data'=>'action','name'=>'action','title'=>'Action','orderable'=>false,'searchable'=>false]);

        return view('administrator.menu.index')->with(compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('administrator.menu.create');
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
            'menuname'  => 'required|string',
            'url'       => 'required|string',
            'parent'    => 'required|numeric',
            'icon'      => 'required|string',
            'level'     => 'required|numeric',
            'resource'  => 'required|string'
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
            'numeric'   => 'Field :attribute harus berupa angka.'
        ];

        $this->validate($request, $rules, $messages);
        
        DB::beginTransaction();

        $data = Menu::create($request->all());

        if($data->save()){
            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>$request->get('menuname') . " successfully saved."
            ]);
        }else{
            DB::rollBack();

            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be saved."
            ]);
        }
        return redirect()->route('menu.index');
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
        $data   = Menu::findOrFail($id);

        return view('administrator.menu.edit',compact('data'));
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
            'menuname'  => 'required|string',
            'url'       => 'required|string',
            'parent'    => 'required|numeric',
            'icon'      => 'required|string',
            'level'     => 'required|numeric',
            'resource'  => 'required|string'
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
            'numeric'   => 'Field :attribute harus berupa angka.'
        ];

        $this->validate($request, $rules, $messages);
        
        DB::beginTransaction();

        $data = Menu::findOrFail($id);
        $data->menuname = $request->menuname;
        $data->icon     = $request->icon;
        $data->url      = $request->url;
        $data->level    = $request->level;
        $data->resource = $request->resource;
        $data->parent   = $request->parent;

        if($data->update()){
            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>$request->get('menuname') . " successfully updated."
            ]);
        }else{
            DB::rollBack();

            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be updated."
            ]);
        }
        return redirect()->route('menu.index');
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

        $delete = Menu::find($id);

        $cek    = UserAccessMenu::where('menu_id',$delete->id)->count();

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
        return redirect()->route('menu.index');
    }
}
