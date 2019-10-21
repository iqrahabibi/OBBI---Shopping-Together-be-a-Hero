<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Builder;
use App\Model\BarangConversi;
use DataTables;
use Session;

class BarangConversiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Builder $htmlBuilder)
    {
        if($request->ajax()){
            $datas = BarangConversi::with('parent')->get();

            return Datatables::of($datas)
            ->addColumn('parent',function($data){
                if($data->parent_id == null){
                    return 'Tidak Ada';
                }
                return '1 ' . $data->parent->satuan;
            })
            ->addColumn('satuan',function($data){
                return $data->jumlah . ' ' . $data->satuan;
            })
            ->addColumn('action',function($data){
                return view('datatables._action',[
                    'model' =>$data,
                    'form_url' =>route('barangconversi.destroy',$data->id),
                    'edit_url'=>route('barangconversi.edit',$data->id),
                    'confirm_message'=>'Yakin mau menghapus '.$data->satuan.' ?'
                ]);
            })
            ->make(true);
        }

        $html=$htmlBuilder
            ->addColumn(['data'=>'parent','name'=>'parent','title'=>'Parent'])
            ->addColumn(['data'=>'satuan','name'=>'satuan','title'=>'Satuan'])
            ->addColumn(['data'=>'action','name'=>'action','title'=>'Action','orderable'=>false,'searchable'=>false]);

        return view('administrator.barangconversi.index')->with(compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('administrator.barangconversi.create');
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
            'satuan'    => 'required|string'
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
        ];

        $this->validate($request, $rules, $messages);
        
        DB::beginTransaction();

        $data = BarangConversi::create([
            'satuan' => $request->get('satuan'),
            'jumlah' => 1,
            'parent_id' => null,
        ]);

        if($request->get('parent_id')){
            $rules      =   [ 
                'jumlah'    => 'required|numeric'
            ];
            
            $messages   =   [
                'required'  => 'Field :attribute harus diisi.',
                'numeric'   => 'Field :attribute harus berupa angka.',
            ];
    
            $this->validate($request, $rules, $messages);

            $parent = BarangConversi::find($request->get('parent_id'));
            $parent->parent_id = $data->id;
            $parent->jumlah = $request->get('jumlah');
            $parent->update();
        }

        if($data->save()){
            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>$request->get('satuan') . " successfully saved."
            ]);
        }else{
            DB::rollBack();

            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be saved."
            ]);
        }
        return redirect()->route('barangconversi.index');
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
        $data = BarangConversi::findOrFail($id);
        $parent = BarangConversi::where('parent_id', $data->id)->first();
        return view('administrator.barangconversi.edit', compact('data', 'parent'));
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
            'satuan'    => 'required|string'
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
        ];

        $this->validate($request, $rules, $messages);
        
        DB::beginTransaction();
        
        $data = BarangConversi::findOrFail($id);
        $data->satuan = $request->get('satuan');

        if($request->get('parent_id')){
            $rules      =   [ 
                'jumlah'    => 'required|numeric'
            ];
            
            $messages   =   [
                'required'  => 'Field :attribute harus diisi.',
                'numeric'   => 'Field :attribute harus berupa angka.',
            ];
    
            $this->validate($request, $rules, $messages);

            $parent = BarangConversi::find($request->get('parent_id'));
            $parent->jumlah = $request->get('jumlah');
            $parent->update();
        }

        if($data->update()){
            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>$request->get('satuan') . " successfully updated."
            ]);
        }else{
            DB::rollBack();
            
            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be updated."
            ]);
        }

        return redirect()->route('barangconversi.index');
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

        $data = BarangConversi::find($id);
        $nama = $data->satuan;

        $parent = BarangConversi::where('parent_id', $data->id)->first();
        $parent->parent_id = null;
        $parent->jumlah = 1;
        $parent->update();

        // TODO : Check data already used

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
        return redirect()->route('barangconversi.index');
    }
}
