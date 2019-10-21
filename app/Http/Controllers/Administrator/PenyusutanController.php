<?php

namespace App\Http\Controllers\Administrator;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Builder;
use App\Helper\Formatting;
use App\Model\Penyusutan;
use App\Model\Asset;
use DataTables;
use Session;

class PenyusutanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Builder $htmlBuilder)
    {
        if($request->ajax()){
            $datas = Penyusutan::with('asset')->get();

            return Datatables::of($datas)
            ->addColumn('asset',function($data){
                return $data->asset->nama;
            })
            ->addColumn('nilai_awal',function($data){
                return Formatting::rupiah($data->nilai_awal);
            })
            ->addColumn('nilai_akhir',function($data){
                return Formatting::rupiah($data->nilai_akhir);
            })
            ->addColumn('action',function($data){
                return view('administrator.penyusutan._action',[
                    'model' =>$data,
                    'form_url' =>route('penyusutan.destroy',$data->id),
                    'confirm_message'=>'Yakin mau menghapus '.$data->nama.' ?'
                ]);
            })->make(true);
        }

        $html=$htmlBuilder
            ->addColumn(['data'=>'asset','name'=>'asset','title'=>'Nama Barang'])
            ->addColumn(['data'=>'nilai_awal','name'=>'nilai_awal','title'=>'Harga Awal'])
            ->addColumn(['data'=>'nilai_akhir','name'=>'nilai_akhir','title'=>'Harga Akhir'])
            ->addColumn(['data'=>'tahun_penyusutan','name'=>'tahun_penyusutan','title'=>'Tahun Penyusutan'])
            ->addColumn(['data'=>'action','name'=>'action','title'=>'Action','orderable'=>false,'searchable'=>false]);

        return view('administrator.penyusutan.index')->with(compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = '';
        return view('administrator.penyusutan.create', compact('data'));
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
            'asset_id'          => 'required|exists:assets,id',
            'tahun_penyusutan'  => 'required|numeric|digits:4',
            'nilai_akhir'       => 'required|numeric',
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
            'exists'    => 'Field :attribute tidak ditemukan.',
            'digits'    => 'Field :attribute harus memiliki panjang :digits.',
        ];
        
        $this->validate($request, $rules, $messages);
        
        DB::beginTransaction();

        $data1 = Asset::findOrfail($request->asset_id);

        $data = Penyusutan::create(array_merge($request->all(),[
            'nilai_awal' => $data1->nilai
        ]));
        
        $data1->nilai = $request->get('nilai_akhir');
        //dd($data1->nilai);
        if($data->save()){
            if($data1->update()){
                DB::commit();

                Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>$request->get('asset_id') . " successfully saved."
                ]);
            }
            
        }else{
            DB::rollBack();

            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be saved."
            ]);
        }
        return redirect()->route('penyusutan.index');
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
        $data = Penyusutan::findOrFail($id);
        return view('administrator.penyusutan.edit', compact('data'));
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
            
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
            'exists'    => 'Field :attribute tidak ditemukan.'
        ];

        $this->validate($request, $rules, $messages);
        
        DB::beginTransaction();
        
        $data = Penyusutan::findOrFail($id);
        $data->nilai_akhir = $request->get('nilai_akhir');
        $data->tahun_penyusutan = $request->get('tahun_penyusutan');

        if($data->update()){
            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>" successfully updated."
            ]);
        }else{
            DB::rollBack();
            
            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be updated."
            ]);
        }

        return redirect()->route('penyusutan.index');
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
        $data = Penyusutan::find($id);
        
        $cari = Penyusutan::where('asset_id',$data->asset_id)->where('created_at','>',$data->created_at)->count();
        
        if($cari == 0){
            if(!$data->delete()) {
                DB::rollBack();
    
                Session::flash("flash_notification",[
                    "level"=>"warning",
                    "message"=>"Data failed to be deleted."
                ]);
                return redirect()->back();
            }
            $update = Asset::where('id',$data->asset_id)->update(['nilai' => $data->nilai_awal]);
            if($request->ajax()) return response()->json(['id'=>$id]);

            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>" successfully deleted."
            ]);
            return redirect()->route('penyusutan.index');
        }
        
        
            DB::rollBack();

            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be deleted."
            ]);
            return redirect()->back();
        
        
    }
}
