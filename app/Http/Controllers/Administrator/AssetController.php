<?php

namespace App\Http\Controllers\Administrator;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Builder;
use App\Helper\Formatting;
use App\Model\Asset;
use App\Model\Penyusutan;
use DataTables;
use Session;

class AssetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Builder $htmlBuilder)
    {
        if($request->ajax()){
            $datas = Asset::query();

            return Datatables::of($datas)
            ->addColumn('nilai',function($data){
                return Formatting::rupiah($data->nilai);
            })
            ->addColumn('action',function($data){
                return view('datatables._action',[
                    'model' =>$data,
                    'form_url' =>route('asset.destroy',$data->id),
                    'edit_url'=>route('asset.edit',$data->id),
                    'confirm_message'=>'Yakin mau menghapus '.$data->nama.' ?'
                ]);
            })->make(true);
        }

        $html=$htmlBuilder
            ->addColumn(['data'=>'nama','name'=>'nama','title'=>'Nama Barang'])
            ->addColumn(['data'=>'nomor','name'=>'nomor','title'=>'Nomor Barang'])
            ->addColumn(['data'=>'tahun','name'=>'tahun','title'=>'Tahun Beli'])
            ->addColumn(['data'=>'nilai','name'=>'nilai','title'=>'Harga Awal'])
            ->addColumn(['data'=>'action','name'=>'action','title'=>'Action','orderable'=>false,'searchable'=>false]);

        return view('administrator.asset.index')->with(compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('administrator.asset.create');
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
            'nama'      => 'required|string',
            'nomor'     => 'required|numeric',
            'tahun'     => 'required|numeric|digits:4',
            'nilai'     => 'required|numeric',
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'numeric'   => 'Field :attribute harus berupa angka.',
            'string'    => 'Field :attribute harus berupa karakter.',
            'digits'    => 'Field :attribute harus memiliki panjang :digits.',
        ];

        $this->validate($request, $rules, $messages);
        
        DB::beginTransaction();

        $data = Asset::create($request->all());

        if($data->save()){
            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>$request->get('nama') . " successfully saved."
            ]);
        }else{
            DB::rollBack();

            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be saved."
            ]);
        }
        return redirect()->route('asset.index');
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
        $data = Asset::findOrFail($id);
        return view('administrator.asset.edit', compact('data'));
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
            'nama'      => 'required|string',
            'nomor'     => 'required|numeric',
            'tahun'     => 'required|numeric|digits:4',
            'nilai'     => 'required|numeric',
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'numeric'   => 'Field :attribute harus berupa angka.',
            'string'    => 'Field :attribute harus berupa karakter.',
            'digits'    => 'Field :attribute harus memiliki panjang :digits.',
        ];

        $this->validate($request, $rules, $messages);
        
        DB::beginTransaction();
        
        $data = Asset::findOrFail($id);
        $data->nama = $request->get('nama');
        $data->nomor = $request->get('nomor');
        $data->tahun = $request->get('tahun');
        $data->nilai = $request->get('nilai');

        if($data->update()){
            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>$request->get('nama') . " successfully updated."
            ]);
        }else{
            DB::rollBack();
            
            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be updated."
            ]);
        }

        return redirect()->route('asset.index');
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

        $data = Asset::find($id);
        $nama = $data->nama;

        $penyusutan = Penyusutan::where('asset_id', $data->id)->count();
        if($penyusutan > 0){
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
        return redirect()->route('asset.index');
    }
}
