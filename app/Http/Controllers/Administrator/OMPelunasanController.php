<?php

namespace App\Http\Controllers\Administrator;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Builder;
use App\Model\OMPelunasan;
use App\Model\OMVoucher;
use App\Model\OMerchantPo;
use DataTables;
use Session;

class OMPelunasanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Builder $htmlBuilder)
    {
        if($request->ajax()){
            $datas = OMPelunasan::with('om_voucher','om_po')->get();
            return Datatables::of($datas)
            ->addColumn('omvoucher',function($data){
                return $data['om_voucher']['jml_om_voucher'];
            })
            ->addColumn('ompo',function($data){
                return $data['om_po']['total_masuk'];
            })
            ->addColumn('action',function($data){
                return view('datatables._action',[
                    'model' =>$data,
                    'form_url' =>route('ompelunasan.destroy',$data->id),
                    'edit_url'=>route('ompelunasan.edit',$data->id),
                    'confirm_message'=>'Yakin mau menghapus ?'
                ]);
            })->make(true);
        }

        $html=$htmlBuilder
            ->addColumn(['data'=>'omvoucher','name'=>'omvoucher','title'=>'ID Voucher'])
            ->addColumn(['data'=>'ompo','name'=>'ompo','title'=>'ID PO'])
            ->addColumn(['data'=>'tanggal','name'=>'tanggal ','title'=>'Tanggal'])
            ->addColumn(['data'=>'action','name'=>'action','title'=>'Action','orderable'=>false,'searchable'=>false]);

        return view('transaksi.omerchantpelunasan.index')->with(compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = '';
        return view('transaksi.omerchantpelunasan.create', compact('data'));
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
            'om_voucher_id'     => 'required|exists:o_m_vouchers,id',
            'om_po_id'          => 'required|exists:o_merchant_pos,id',
            'tanggal'           => 'required|date',
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
            'exists'    => 'Field :attribute tidak ditemukan.'
        ];

        $this->validate($request, $rules, $messages);
        DB::beginTransaction();

        $update = OMVoucher::findOrfail($request->om_voucher_id);
        
        $cek = OMerchantPo::findOrfail($request->om_po_id);
        $data = OMPelunasan::create($request->all());
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
        return redirect()->route('ompelunasan.index');
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
        $data = OMPelunasan::findOrFail($id);
        return view('transaksi.omerchantpelunasan.edit', compact('data'));
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
        
        $data = OMPelunasan::findOrFail($id);
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

        return redirect()->route('ompelunasan.index');
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

        $data = OMPelunasan::find($id);

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
        return redirect()->route('ompelunasan.index');
    }
}
