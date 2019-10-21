<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Builder;

use App\Helper\Formatting;
use App\Model\Finance;
use DataTables;
use Session;

class FinanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Builder $htmlBuilder)
    {

        if($request->ajax()){
            $datas = Finance::all();

            return Datatables::of($datas)
            ->addColumn('keuntungan',function($data){
                return Formatting::rupiah($data->keuntungan);
            })
            ->addColumn('amal',function($data){
                return Formatting::rupiah($data->amal);
            })
            ->addColumn('valid',function($data){
                if($data->valid == 1){
                    return 'Yes';
                }else{
                    return 'No';
                }
            })
            ->addColumn('action',function($data){
                if($data->valid == 0){
                    return 'No Action';
                }
                return view('administrator.finance._action',[
                    'model' =>$data,
                    'edit_url'=>route('finance.edit',$data->id),
                ]);
            })
            ->make(true);
        }

        $html=$htmlBuilder
            ->addColumn(['data'=>'kode','name'=>'kode','title'=>'Kode'])
            ->addColumn(['data'=>'deskripsi','name'=>'deskripsi','title'=>'Deskripsi'])
            ->addColumn(['data'=>'keuntungan','name'=>'keuntungan','title'=>'Keuntungan'])
            ->addColumn(['data'=>'amal','name'=>'amal','title'=>'Amal'])
            ->addColumn(['data'=>'valid','name'=>'valid','title'=>'Valid'])
            ->addColumn(['data'=>'action','name'=>'action','title'=>'Action','orderable'=>false,'searchable'=>false]);

        return view('administrator.finance.index')->with(compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('administrator.finance.create');
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
            'kode' => 'required|string|max:10',
            'deskripsi' => 'required|string|max:150',
            'keuntungan' => 'required|numeric',
            'amal' => 'required|numeric',
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
            'numeric'   => 'Field :attribute harus berupa angka.',
        ];

        $this->validate($request, $rules, $messages);
        
        DB::beginTransaction();

        $cek = Finance::where('kode', $request->get('kode'))->first();
        if($cek){
            $cek->valid = 0;
            $cek->update();
        }

        $data = Finance::create(array_merge($request->all(), [
            'valid' => 1
        ]));

        if($data->save()){
            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>$request->get('kode') . " successfully saved."
            ]);
        }else{
            DB::rollBack();

            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be saved."
            ]);
        }
        return redirect()->route('finance.index');
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
        $data = Finance::findOrFail($id);
        return view('administrator.finance.edit', compact('data'));
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
            'kode' => 'required|string|max:10',
            'deskripsi' => 'required|string|max:150',
            'keuntungan' => 'required|numeric',
            'amal' => 'required|numeric',
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
            'numeric'   => 'Field :attribute harus berupa angka.',
        ];

        $this->validate($request, $rules, $messages);
        
        DB::beginTransaction();
        
        $data = Finance::findOrFail($id);
        $data->kode = $request->get('kode');
        $data->deskripsi = $request->get('deskripsi');
        $data->keuntungan = $request->get('keuntungan');
        $data->amal = $request->get('amal');
        $data->valid = $request->get('valid');

        if($data->update()){
            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>$request->get('kode') . " successfully updated."
            ]);
        }else{
            DB::rollBack();
            
            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be updated."
            ]);
        }

        return redirect()->route('finance.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        return redirect()->route('finance.index');
    }
}
