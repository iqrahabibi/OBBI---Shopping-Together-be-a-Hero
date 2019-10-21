<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Builder;
use App\Helper\Formatting;
use App\OBBI\obbiHelper as DATA;
use App\Model\Usaha;
use App\Model\OMerchant;
use App\Model\UsahaOMerchant;
use App\Model\User;
use App\Model\Role;
use Carbon\Carbon;
use DataTables;
use Session;

class UsahaOMerchantController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Builder $htmlBuilder)
    {
        if($request->ajax()){
            $datas = UsahaOMerchant::with('usaha','o_merchant.user')->get();

            return Datatables::of($datas)
            ->addColumn('usaha',function($data){
                return $data->usaha->nama_usaha;
            })
            ->addColumn('user',function($data){
                return $data->o_merchant->user->fullname;
            })
            ->addColumn('email',function($data){
                return $data->o_merchant->user->email;
            })
            ->addColumn('tanggal_masuk',function($data){
                if(empty($data->tanggal_masuk)){
                    return '-';
                }
                return Carbon::createFromFormat('Y-m-d', $data->tanggal_masuk)->format('j F Y');
            })
            ->addColumn('tanggal_keluar',function($data){
                if(empty($data->tanggal_masuk)){
                    return '-';
                }
                return Carbon::createFromFormat('Y-m-d', $data->tanggal_keluar)->format('j F Y');
            })
            ->addColumn('modal',function($data){
                return Formatting::rupiah($data->modal);
            })
            ->addColumn('porsi',function($data){
                return Formatting::percent($data->porsi);
            })
            ->addColumn('action',function($data){
                return view('datatables._action',[
                    'model' =>$data,
                    'form_url' =>route('usahaomerchant.destroy',$data->id),
                    'edit_url'=>route('usahaomerchant.edit',$data->id),
                    'confirm_message'=>'Yakin mau menghapus data ?'
                ]);
            })->make(true);
        }

        $html=$htmlBuilder
            ->addColumn(['data'=>'usaha','name'=>'usaha','title'=>'Usaha'])
            ->addColumn(['data'=>'user','name'=>'user','title'=>'Fullname'])
            ->addColumn(['data'=>'email','name'=>'email','title'=>'Email'])
            ->addColumn(['data'=>'kode','name'=>'kode','title'=>'Kode'])
            ->addColumn(['data'=>'type','name'=>'type','title'=>'Type'])
            ->addColumn(['data'=>'modal','name'=>'modal','title'=>'Modal'])
            ->addColumn(['data'=>'porsi','name'=>'porsi','title'=>'Porsi'])
            ->addColumn(['data'=>'tanggal_masuk','name'=>'tanggal_masuk','title'=>'Tanggal Masuk'])
            ->addColumn(['data'=>'tanggal_keluar','name'=>'tanggal_keluar','title'=>'Tanggal Keluar'])
            ->addColumn(['data'=>'action','name'=>'action','title'=>'Action','orderable'=>false,'searchable'=>false]);

        return view('administrator.usahaomerchant.index')->with(compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('administrator.usahaomerchant.create');
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
            'usaha_id'          => 'required|exists:usahas,id',
            'o_merchant_id'     => 'required|exists:o_merchants,id',
            'modal'             => 'required|numeric',
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
            'numeric'   => 'Field :attribute harus berupa angka.',
        ];

        $this->validate($request, $rules, $messages);
        
        DB::beginTransaction();

        $kode = DATA::autonumber_omerchant('usaha_o_merchants','kode','OM-');
        $porsi = 0;
        $type = 'TB';

        $cari_usaha_om = UsahaOMerchant::where('usaha_id',$request->get('usaha_id'))->first();
        if(!empty($cari_usaha_om)){
            $kode = $cari_usaha_om->kode;
        }

        $modal_terkumpul = UsahaOMerchant::where('usaha_id',$request->get('usaha_id'))->sum('modal');

        $usaha = Usaha::find($request->get('usaha_id'));
        if(($modal_terkumpul + $request->get('modal')) > $usaha->total_modal){
            DB::rollBack();

            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Total modal berlebih. Silahkan periksa kembali."
            ]);
            return redirect()->back();
        }

        if($usaha->total_modal == $request->get('modal')){
            $type = 'TP';
            $porsi = 100;
        }else{
            $porsi = ($request->get('modal') / $usaha->total_modal) * 100;
        }

        $data = UsahaOMerchant::updateOrCreate([
            'usaha_id' => $request->get('usaha_id'),
            'o_merchant_id' => $request->get('o_merchant_id'),
        ], array_merge($request->all(), [
            'kode' => $kode,
            'type' => $type,
            'porsi' => $porsi,
            'valid' => 1,
        ]));

        $cek_modal_terkumpul = UsahaOMerchant::where('usaha_id',$usaha->id)->sum('modal');
        if($porsi == 100 || $usaha->total_modal == $cek_modal_terkumpul){
           $usaha->status = 'Closed';
           $usaha->update(); 
        }

        $om = OMerchant::where('id', $data->o_merchant_id)->first();
        $datauser = User::where('id', $om->user_id)->first();
        if(!$datauser->hasRole(Role::find(5)->name)){
            $datauser->roles()->attach(5);
        }

        /**
         * CREATE CASH OBBI
         */
        // $jumlah_one_merchant = 45000000;
        // $cashobbi = CashObbi::updateOrCreate([
        //     'kode' => $kode,
        // ], array_merge($request->all(), [
        //     'kode' => $kode,
        //     'tipe' => 'OMerchant',
        //     'jumlah' => $jumlah_one_merchant,
        //     'cash' => 1,
        //     'status' => 'Masuk'
        // ]));
        
        if($data->save()){
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
        return redirect()->route('usahaomerchant.index');
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
        $data = UsahaOMerchant::with('usaha','o_merchant')->findOrFail($id);
        return view('administrator.usahaomerchant.edit', compact('data'));
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
            'usaha_id'          => 'required|exists:usahas,id',
            'o_merchant_id'     => 'required|exists:o_merchants,id',
            'modal'             => 'required|numeric',
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
            'numeric'   => 'Field :attribute harus berupa angka.',
        ];

        $this->validate($request, $rules, $messages);
        
        DB::beginTransaction();

        $data = UsahaOMerchant::findOrFail($id);

        $modal_terkumpul = UsahaOMerchant::where('usaha_id',$request->get('usaha_id'))->sum('modal');

        $usaha = Usaha::find($request->get('usaha_id'));

        $modal = $request->get('modal');

        if((($modal_terkumpul - $data->modal) + $modal) > $usaha->total_modal){
            DB::rollBack();

            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Total modal berlebih. Silahkan periksa kembali."
            ]);
            return redirect()->back();
        }

        $porsi = ($modal / $usaha->total_modal) * 100;
        $data->modal = $modal;
        $data->porsi = $porsi;
        $data->update();

        $cek_modal_terkumpul = UsahaOMerchant::where('usaha_id',$usaha->id)->sum('modal');
        if($usaha->total_modal == $cek_modal_terkumpul){
           $usaha->status = 'Closed';
           $usaha->update(); 
        }

        $om = OMerchant::where('id', $data->o_merchant_id)->first();
        $datauser = User::where('id', $om->user_id)->first();
        if(!$datauser->hasRole(Role::find(5)->name)){
            $datauser->roles()->attach(5);
        }

        if($request->get('tanggal_masuk')){
            $data->tanggal_masuk = $request->get('tanggal_masuk');
        }
        if($request->get('tanggal_keluar')){
            $data->tanggal_keluar = $request->get('tanggal_keluar');
        }

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
        return redirect()->route('usahaomerchant.index');
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

        $data = UsahaOMerchant::with('usaha','o_merchant')->find($id);
        $nama = $data->usaha->nama_usaha;

        $usaha = Usaha::find($data->usaha_id);
        $usaha->status = 'Open';
        $usaha->update();

        // $cashobbi = CashObbi::where('kode', $data->kode)->first();
        $usaha_om_lain = UsahaOMerchant::where('usaha_id',$usaha->id)
                ->where('id','!=', $data->id)
                ->count();

        /**
         * Check jika User masih memiliki Usaha sebagai OMerchant Owner
         */
        $datauser = User::where('id', $data->o_merchant->user_id)->first();
        $usaha_om_lain_by_datauser = UsahaOMerchant::with('o_merchant')
            ->whereHas('o_merchant', function($om) use ($datauser){
                $om->where('user_id', $datauser->id);
            })
            ->where('id','!=', $data->id)
            ->count();
        if($usaha_om_lain_by_datauser > 0){
            // Tidak perlu detach Role OMerchant Owner
        }else{
            $datauser->roles()->detach(5);
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
        return redirect()->route('usahaomerchant.index');
    }
}
