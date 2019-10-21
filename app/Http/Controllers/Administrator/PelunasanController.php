<?php

namespace App\Http\Controllers\Administrator;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Builder;
use App\Model\Pelunasan;
use App\Model\Voucher;
use App\Model\PurchasingOrder;
use DataTables;
use Session;

class PelunasanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Builder $htmlBuilder)
    {
        if($request->ajax()){
            $datas = Pelunasan::with('voucher','purchasing_order')->get();
            return Datatables::of($datas)
            ->addColumn('voucher',function($data){
                return $data->voucher->jml_voucher;
            })
            ->addColumn('po',function($data){
                return $data->purchasing_order->total_masuk;
            })
            ->addColumn('action',function($data){
                return view('datatables._action',[
                    'model' =>$data,
                    'form_url' =>route('pelunasan.destroy',$data->id),
                    'edit_url'=>route('pelunasan.edit',$data->id),
                    'confirm_message'=>'Yakin mau menghapus ?'
                ]);
            })->make(true);
        }

        $html=$htmlBuilder
            ->addColumn(['data'=>'voucher','name'=>'voucher','title'=>'ID Voucher'])
            ->addColumn(['data'=>'po','name'=>'po','title'=>'ID PO'])
            ->addColumn(['data'=>'tanggal','name'=>'tanggal ','title'=>'Tanggal'])
            ->addColumn(['data'=>'action','name'=>'action','title'=>'Action','orderable'=>false,'searchable'=>false]);

        return view('administrator.pelunasan.index')->with(compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = '';
        return view('administrator.pelunasan.create', compact('data'));
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
            'voucher_id'            => 'required|exists:vouchers,id',
            'purchasing_order_id'   => 'required|exists:purchasing_orders,id',
            'tanggal'               => 'required|date',
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
            'exists'    => 'Field :attribute tidak ditemukan.'
        ];

        $this->validate($request, $rules, $messages);
        
        DB::beginTransaction();

        $update = Voucher::findOrfail($request->voucher_id);
        $cek = PurchasingOrder::findOrfail($request->purchasing_order_id);
        $data = Pelunasan::create($request->all());
        if($update->sisa > $cek->total_masuk){
            $hasil = $update->sisa-$cek->total_masuk;
        }else{
            $hasil=0;
        }
        
        $update->sisa = $hasil;
        
        if($data->save()){
            $update->update();
            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>"Data successfully saved."
            ]);
        }else{
            DB::rollBack();

            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be saved."
            ]);
        }
        return redirect()->route('pelunasan.index');
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
        $data = Pelunasan::findOrFail($id);
        return view('administrator.pelunasan.edit', compact('data'));
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
            'tanggal'   => 'required|date',
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
            'exists'    => 'Field :attribute tidak ditemukan.'
        ];

        $this->validate($request, $rules, $messages);
        
        DB::beginTransaction();
        
        $data = Pelunasan::findOrFail($id);
        $data->tanggal = $request->get('tanggal');

        if($data->update()){
            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>"Data successfully updated."
            ]);
        }else{
            DB::rollBack();
            
            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be updated."
            ]);
        }

        return redirect()->route('pelunasan.index');
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

        $data = Pelunasan::find($id);

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
            "message"=>"Data successfully deleted."
        ]);
        return redirect()->route('pelunasan.index');
    }
}
