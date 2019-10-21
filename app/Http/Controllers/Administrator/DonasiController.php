<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Builder;
use App\Helper\Formatting;
use Carbon\Carbon;
use App\Model\Donasi;
use App\Model\DetailUser;
use App\Model\TargetDonasi;
use App\Model\Saldo;
use DataTables;
use Session;

class DonasiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Builder $htmlBuilder)
    {
        if($request->ajax()){
            $datas = Donasi::with('target_donasi.tipe_donasi', 'target_donasi.agama', 'detail_user.user')->get();

            return Datatables::of($datas)
            ->addColumn('user',function($data){
                return $data->detail_user->user->fullname;
            })
            ->addColumn('email',function($data){
                return $data->detail_user->user->email;
            })
            ->addColumn('target_donasi',function($data){
                return $data->target_donasi->tipe_donasi->nama_tipe_donasi . ' - ' . 
                    $data->target_donasi->agama->nama_agama . ' - ' . 
                    $data->target_donasi->nama_target_donasi;
            })
            ->addColumn('waktu',function($data){
                return Carbon::createFromFormat('Y-m-d H:i:s', $data->updated_at)->format('j F Y, H:i:s');
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
                return view('administrator.donasi._action',[
                    'model' =>$data,
                    'form_url' =>route('donasi.destroy',$data->id),
                    'confirm_message'=>'Yakin mau menghapus donasi untuk ' . $data->detail_user->user->fullname . ' ini ?'
                ]);
            })
            ->make(true);
        }

        $html=$htmlBuilder
            ->addColumn(['data'=>'user','name'=>'user','title'=>'Fullname'])
            ->addColumn(['data'=>'email','name'=>'email','title'=>'Email'])
            ->addColumn(['data'=>'target_donasi','name'=>'target_donasi','title'=>'Target Donasi'])
            ->addColumn(['data'=>'awal','name'=>'awal','title'=>'Saldo Awal'])
            ->addColumn(['data'=>'jumlah','name'=>'jumlah','title'=>'Jumlah Donasi'])
            ->addColumn(['data'=>'akhir','name'=>'akhir','title'=>'Saldo Akhir'])
            ->addColumn(['data'=>'waktu','name'=>'waktu','title'=>'Waktu'])
            ->addColumn(['data'=>'action','name'=>'action','title'=>'Action','orderable'=>false,'searchable'=>false]);

        return view('administrator.donasi.index')->with(compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('administrator.donasi.create');
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
            'detail_user_id'    => 'required|exists:detail_users,id',
            'target_donasi_id'  => 'required',
            'jumlah'            => 'required',
        ];

        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'exists'    => 'Field :attribute tidak ditemukan.',
            'numeric'   => 'Field :attribute harus berupa angka.',
        ];

        $this->validate($request, $rules, $messages);
        
        DB::beginTransaction();

        try{
            $detil_user_id = $request->get('detail_user_id');

            $detailuser = DetailUser::with('user')->where('id', $detil_user_id)->first();
            $saldouser = Saldo::where('user_id', $detailuser->user->id)->first();

            $target_donasis = $request->get('target_donasi_id');
            $jumlahs = $request->get('jumlah');

            $akhir = null;
            $total_saldo = $saldouser->amal;
            $total_donasi = 0;

            foreach($target_donasis as $index => $target_donasi){
                $awal = $saldouser->amal;
                $jumlah = $jumlahs[$index];
                $akhir = $awal - $jumlah;

                Donasi::create([
                    'target_donasi_id' => $target_donasi,
                    'detail_user_id' => $detil_user_id,
                    'awal' => $awal,
                    'jumlah' => $jumlah,
                    'akhir' => $akhir
                ]);

                $saldouser->amal = $akhir;
                $saldouser->update();

                $total_donasi += $jumlah;
            }

            if($total_saldo != $total_donasi){
                DB::rollBack();

                Session::flash("flash_notification",[
                    "level"=>"warning",
                    "message"=>"Saldo harus dihabiskan."
                ]);
                return redirect()->back();
            }

            if($akhir < 0){
                DB::rollBack();

                Session::flash("flash_notification",[
                    "level"=>"warning",
                    "message"=>"Saldo not enough."
                ]);
                return redirect()->back();
            }

            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>"Donasi for " . $detailuser->user->fullname . " successfully saved."
            ]);
        }catch(Exception $e){
            DB::rollBack();

            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be saved."
            ]);
        }
        return redirect()->route('donasi.index');
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
        return redirect()->route('donasi.index');
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
        return redirect()->route('donasi.index');
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

        $data = Donasi::find($id);

        $detailuser = DetailUser::with('user')->where('id', $data->detail_user_id)->first();
        $saldouser = Saldo::where('user_id', $detailuser->user->id)->first();
        $saldouser->amal = $saldouser->amal + $data->jumlah;
        $saldouser->update();

        $cek = Donasi::where('updated_at', '>', $data->updated_at)->count();
        if($cek > 0){
            DB::rollBack();

            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be deleted. The newest data exists."
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

        Session::flash("flash_notification",[
            "level"=>"success",
            "message"=>"Donasi for " . $detailuser->user->fullname . " successfully deleted."
        ]);
        
        DB::commit();

        return redirect()->route('donasi.index');
    }

    public function create_kelurahan()
    {
        return view('administrator.donasi.create_kelurahan');
    }

    public function store_kelurahan(Request $request)
    {
        $rules      =   [ 
            'target_donasi_id'  => 'required',
            'persentase'        => 'required',
            'jumlah'            => 'required',
            'total_amal'        => 'required|numeric',
            'total_donasi'      => 'required|numeric',
        ];

        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'exists'    => 'Field :attribute tidak ditemukan.',
            'numeric'   => 'Field :attribute harus berupa angka.',
        ];

        $this->validate($request, $rules, $messages);

        DB::beginTransaction();

        try{
            $kelurahan_id = $request->get('kelurahan_id');
            $target_donasis = $request->get('target_donasi_id');
            $persentases = $request->get('persentase');
            $target_donasis = $request->get('target_donasi_id');
            
            $cek = 0;
            foreach($persentases as $index => $persentase){
                $cek += $persentase;
            }
            if($cek != 100){
                Session::flash("flash_notification",[
                    "level"=>"warning",
                    "message"=>"Total persentase harus 100 %."
                ]);
                return redirect()->back();
            }

            foreach($target_donasis as $index => $target_donasi){
                $target = TargetDonasi::findOrFail($target_donasi);
                $details = DetailUser::with('user.saldo')->where('kelurahan_id', $target->kelurahan_id)->get();

                $list = array();
                $persentase = $persentases[$index];
                foreach($details as $detail){
                    if(!empty($detail->user->saldo)){

                        $awal = $detail->user->saldo->amal;
                        $total = ($awal * $persentase) / 100;
                        $akhir = $awal - $total;

                        Donasi::create([
                            'target_donasi_id' => $target->id,
                            'detail_user_id' => $detail->id,
                            'awal' => $awal,
                            'jumlah' => $total,
                            'akhir' => 0,
                            'created_at' => Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s'),
                            'updated_at' => Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s')
                        ]);
                    }
                }
            }

            $details = DetailUser::with('user.saldo')->where('kelurahan_id', $target->kelurahan_id)->get();

            foreach($details as $detail){
                if(!empty($detail->user->saldo)){
                    $saldo = Saldo::findOrFail($detail->user->saldo->id);
                    $saldo->amal = 0;
                    $saldo->update();
                }
            }
                
            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>"Donasi successfully saved."
            ]);

        }catch(Exception $e){
            DB::rollBack();

            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be saved."
            ]);
        }
        
        return redirect()->route('donasi.index');
    }
}
