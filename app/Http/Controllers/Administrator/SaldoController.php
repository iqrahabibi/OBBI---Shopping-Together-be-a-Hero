<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Builder;
use App\Helper\Formatting;
use Carbon\Carbon;
use App\Model\Saldo;
use App\Model\DigiPay;
use DataTables;
use Session;

class SaldoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Builder $htmlBuilder)
    {
        if($request->ajax()){
            $datas = Saldo::with('user')
                ->whereHas('user', function($user){
                    $user->whereHas('roles', function($role){
                        $role->where('role_id', 1);
                    });
                })->get();

            return Datatables::of($datas)
            ->addColumn('user',function($data){
                return $data->user->fullname;
            })
            ->addColumn('email',function($data){
                return $data->user->email;
            })
            ->addColumn('saldo',function($data){
                return Formatting::rupiah($data->saldo);
            })
            ->addColumn('amal',function($data){
                return Formatting::rupiah($data->amal);
            })
            ->addColumn('keuntungan',function($data){
                return Formatting::rupiah($data->keuntungan);
            })
            ->addColumn('action',function($data){
                return view('administrator.saldo._action',[
                    'history_url' =>route('saldo.history',$data->user->id)
                ]);
            })
            ->make(true);
        }

        $html=$htmlBuilder
            ->addColumn(['data'=>'user','name'=>'user','title'=>'Fullname'])
            ->addColumn(['data'=>'email','name'=>'email','title'=>'Email'])
            ->addColumn(['data'=>'saldo','name'=>'saldo','title'=>'Saldo Akhir'])
            ->addColumn(['data'=>'amal','name'=>'amal','title'=>'Amal'])
            ->addColumn(['data'=>'keuntungan','name'=>'keuntungan','title'=>'Keuntungan'])
            ->addColumn(['data'=>'action','name'=>'action','title'=>'Action','orderable'=>false,'searchable'=>false]);

        return view('administrator.saldo.index')->with(compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return redirect()->route('saldo.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return redirect()->route('saldo.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->route('saldo.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect()->route('saldo.index');
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
        return redirect()->route('saldo.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return redirect()->route('saldo.index');
    }

    public function history(Request $request, Builder $htmlBuilder, $id)
    {
        $datas = DigiPay::with('user')
            ->where('user_id', $id)
            ->get();

        if($request->ajax()){
            return Datatables::of($datas)
            ->addColumn('user',function($data){
                return $data->user->fullname;
            })
            ->addColumn('email',function($data){
                return $data->user->email;
            })
            ->addColumn('awal',function($data){
                return Formatting::rupiah($data->awal);
            })
            ->addColumn('jumlah',function($data){
                return Formatting::rupiah($data->jumlah);
            })
            ->addColumn('akhir',function($data){
                return Formatting::rupiah($data->akhir);
            })
            ->addColumn('action',function($data){
                if(empty($data->invoice)){
                    return 'No action';
                }
                if($data->valid == 1){
                    return 'Sudah divalidasi.';
                }
                return view('administrator.saldo._validasi',[
                    'model' =>$data,
                    'validasi_url' =>route('saldo.validasi',$data->id),
                    'confirm_message'=>'Yakin mau validasi data ini ?'
                ]);
            })
            ->make(true);
        }

        $html=$htmlBuilder
            ->addColumn(['data'=>'user','name'=>'user','title'=>'Fullname'])
            ->addColumn(['data'=>'email','name'=>'email','title'=>'Email'])
            ->addColumn(['data'=>'invoice','name'=>'invoice','title'=>'Invoice'])
            ->addColumn(['data'=>'awal','name'=>'awal','title'=>'Awal'])
            ->addColumn(['data'=>'jumlah','name'=>'jumlah','title'=>'Jumlah'])
            ->addColumn(['data'=>'akhir','name'=>'akhir','title'=>'Akhir'])
            ->addColumn(['data'=>'notes','name'=>'notes','title'=>'Notes'])
            ->addColumn(['data'=>'kode','name'=>'kode','title'=>'Kode'])
            ->addColumn(['data'=>'phone','name'=>'phone','title'=>'Phone'])
            ->addColumn(['data'=>'trxid','name'=>'trxid','title'=>'Administrator ID'])
            ->addColumn(['data'=>'action','name'=>'action','title'=>'Action','orderable'=>false,'searchable'=>false]);

        return view('administrator.saldo.history')->with(compact('html'));
    }

    public function validasi(Request $request, $id)
    {
        DB::beginTransaction();

        $data = DigiPay::where('id', $id)->first();

        $saldouser = Saldo::where('user_id', $data->user_id)->first();
        $saldouser->saldo = $saldouser->saldo + $data->jumlah;
        
        $data->valid = 1;
        $data->kode = 0;

        if($saldouser->update() && $data->update()){
            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>"Data berhasil diubah."
            ]);
        }else{
            DB::rollBack();

            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Gagal mengubah data."
            ]);
        }
        return redirect()->back();
    }
}
