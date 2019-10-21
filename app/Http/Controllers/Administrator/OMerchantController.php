<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\Html\Builder;
use Carbon\Carbon;
use App\OBBI\obbiHelper as DATA;
use App\Model\OMerchant;
use App\Model\ReferalOMerchant;
use App\Model\CashObbi;
use App\Model\User;
use App\Model\Role;
use DataTables;
use Session;

class OMerchantController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Builder $htmlBuilder)
    {
        if($request->ajax()){
            $datas = OMerchant::with('user')->get();

            return Datatables::of($datas)
            ->addColumn('user',function($data){
                return $data->user->fullname;
            })
            ->addColumn('email',function($data){
                return $data->user->email;
            })
            ->addColumn('action',function($data){
                return view('administrator.omerchant._action',[
                    'model' =>$data,
                    'form_url' =>route('omerchant.destroy',$data->id),
                    'edit_url'=>route('omerchant.edit',$data->id),
                    'confirm_message'=>'Yakin mau menghapus '.$data->user->fullname.' ?',
                    'referal_url' => route('omerchant.referal',$data->user->id)
                ]);
            })->make(true);
        }

        $html=$htmlBuilder
            ->addColumn(['data'=>'user','name'=>'user','title'=>'Fullname'])
            ->addColumn(['data'=>'email','name'=>'email','title'=>'Email'])
            ->addColumn(['data'=>'referal','name'=>'referal','title'=>'referal'])
            ->addColumn(['data'=>'action','name'=>'action','title'=>'Action','orderable'=>false,'searchable'=>false]);

        return view('administrator.omerchant.index')->with(compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('administrator.omerchant.create');
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
            'user_id'   => 'required|exists:users,id',
            'referal'   => 'required|string|size:10',
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
            'numeric'   => 'Field :attribute harus berupa angka.',
            'size'    => 'Field :attribute harus memiliki panjang :size.',
        ];

        $this->validate($request, $rules, $messages);
        
        DB::beginTransaction();

        $data = OMerchant::updateOrCreate([
            'user_id' => $request->get('user_id')
        ], $request->all());

        if($request->get('referal_omerchant')){
            $cari = OMerchant::with('user')->where('referal', $request->get('referal_omerchant'))->first();
            if(!empty($cari)){
                ReferalOMerchant::create([
                    'user_id' => $cari->user->id,
                    'o_merchant_id' => $data->id,
                    'valid' => 1,
                ]);
            }else{
                DB::rollBack();

                Session::flash("flash_notification",[
                    "level"=>"warning",
                    "message"=>"Kode yang direferalin tidak ditemukan."
                ]);

                return redirect()->back();
            }
        }
        
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
        return redirect()->route('omerchant.index');
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
        $data = OMerchant::with('user')->findOrFail($id);
        return view('administrator.omerchant.edit', compact('data'));
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
            'referal'   => 'required|string|size:10',
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'string'    => 'Field :attribute harus berupa karakter.',
            'numeric'   => 'Field :attribute harus berupa angka.',
            'size'    => 'Field :attribute harus memiliki panjang :size.',
        ];

        $this->validate($request, $rules, $messages);
        
        DB::beginTransaction();
        
        $data = OMerchant::with('user')->findOrFail($id);
        $data->referal = $request->get('referal');

        if($data->update()){
            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>$data->user->fullname . " successfully updated."
            ]);
        }else{
            DB::rollBack();
            
            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be updated."
            ]);
        }

        return redirect()->route('omerchant.index');
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

        $data = OMerchant::with('user')->find($id);
        $nama = $data->user->fullname;

        $cari = ReferalOMerchant::where('o_merchant_id', $data->id)->first();
        if(!empty($cari)){
            $cari->delete();
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
        return redirect()->route('omerchant.index');
    }

    public function randomreferal(){
        return Str::upper(Str::random(10));
    }

    public function referal_omerchant(Request $request, Builder $htmlBuilder, $id)
    {
        $datas = ReferalOMerchant::with('user', 'o_merchant.user')
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
            ->addColumn('referal',function($data){
                return $data->o_merchant->user->fullname;
            })
            ->addColumn('email_referal',function($data){
                return $data->o_merchant->user->email;
            })
            ->addColumn('waktu',function($data){
                return Carbon::createFromFormat('Y-m-d H:i:s', $data->updated_at)->format('j F Y, H:i:s');
            })
            ->make(true);
        }

        $html=$htmlBuilder
            ->addColumn(['data'=>'user','name'=>'user','title'=>'User OMerchant'])
            ->addColumn(['data'=>'email','name'=>'email','title'=>'Email OMerchant'])
            ->addColumn(['data'=>'referal','name'=>'referal','title'=>'User Referral'])
            ->addColumn(['data'=>'email_referal','name'=>'email_referal','title'=>'Email Referral'])
            ->addColumn(['data'=>'waktu','name'=>'waktu','title'=>'Waktu']);

        return view('administrator.omerchant.referal')->with(compact('html'));
    }
}
