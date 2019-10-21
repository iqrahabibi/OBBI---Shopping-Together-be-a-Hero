<?php

namespace App\Http\Controllers\Administrator;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Builder;
use App\Model\Category;
use App\Model\Produk;
use DataTables;
use Session;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Builder $htmlBuilder)
    {
        if($request->ajax()){
            $datas = Category::with('group')->get();

            return Datatables::of($datas)
            ->addColumn('group',function($data){
                return $data->group->nama_group;
            })
            ->addColumn('valid',function($data){
                if($data->valid == 1){
                    return 'Yes';
                }
                return 'No';
            })
            ->addColumn('action',function($data){
                return view('datatables._action',[
                    'model' =>$data,
                    'form_url' =>route('category.destroy',$data->id),
                    'edit_url'=>route('category.edit',$data->id),
                    'confirm_message'=>'Yakin mau menghapus '.$data->nama_kategori.' ?'
                ]);
            })->make(true);
        }

        $html=$htmlBuilder
            ->addColumn(['data'=>'group','name'=>'group','title'=>'Nama Group'])
            ->addColumn(['data'=>'nama_kategori','name'=>'nama_kategori','title'=>'Nama Kategori'])
            ->addColumn(['data'=>'valid','name'=>'valid','title'=>'Valid'])
            ->addColumn(['data'=>'action','name'=>'action','title'=>'Action','orderable'=>false,'searchable'=>false]);

        return view('administrator.category.index')->with(compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = '';
        return view('administrator.category.create', compact('data'));
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
            'group_id'          => 'required|exists:groups,id',
            'nama_kategori'     => 'required|string'
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
            'exists'    => 'Field :attribute tidak ditemukan.'
        ];

        $this->validate($request, $rules, $messages);
        
        DB::beginTransaction();

        $data = Category::create(array_merge($request->all(), [
            'valid' => 1
        ]));

        if($data->save()){
            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>$request->get('nama_kategori') . " successfully saved."
            ]);
        }else{
            DB::rollBack();

            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be saved."
            ]);
        }
        return redirect()->route('category.index');
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
        $data = Category::findOrFail($id);
        return view('administrator.category.edit', compact('data'));
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
            'nama_kategori'     => 'required|string'
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
            'exists'    => 'Field :attribute tidak ditemukan.'
        ];

        $this->validate($request, $rules, $messages);
        
        DB::beginTransaction();
        
        $data = Category::findOrFail($id);
        $data->nama_kategori = $request->get('nama_kategori');
        $data->valid = $request->get('valid');

        if($data->update()){
            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>$request->get('nama_kategori') . " successfully updated."
            ]);
        }else{
            DB::rollBack();
            
            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be updated."
            ]);
        }

        return redirect()->route('category.index');
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

        $data = Category::find($id);
        $nama = $data->nama_kategori;

        $produk = 0;
        if($produk > 0){
            Session::flash("flash_notification",[
                "level"=>"danger",
                "message"=>"Data already used."
            ]);
            return redirect()->back();
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
        return redirect()->route('category.index');
    }
}
