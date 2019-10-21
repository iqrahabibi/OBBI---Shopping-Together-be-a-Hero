<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Builder;
use App\Model\Tabungan;
use App\Model\CashObbi;
use App\Model\OMerchant;
use App\Model\UsahaOMerchant;
use App\Model\User;
use DataTables;
use Session;
use Carbon\Carbon;

class TabunganController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Builder $htmlBuilder)
    {
        if($request->ajax()){
            $datas = Tabungan::with('usaha_o_merchant.o_merchant.user', 'usaha_o_merchant.usaha')->get();

            return Datatables::of($datas)
            ->addColumn('user',function($data){
                if(empty($data->usaha_o_merchant)){
                    return '';
                }
                return $data->usaha_o_merchant->o_merchant->user->fullname;
            })
            ->addColumn('usaha_o_merchant',function($data){
                if(empty($data->usaha_o_merchant)){
                    return '';
                }
                return $data->usaha_o_merchant->usaha->nama_usaha;
            })
            ->addColumn('waktu',function($data){
                return Carbon::createFromFormat('Y-m-d H:i:s', $data->created_at)->format('j F Y, H:i:s');
            })
            ->addColumn('action',function($data){
                return view('administrator.tabungan._action',[
                    'model' =>$data,
                    'form_url' =>route('tabungan.destroy',$data->id),
                    'confirm_message'=>'Yakin mau menghapus '.$data->nama_tabungan.' ?'
                ]);
            })->make(true);
        }

        $html=$htmlBuilder
            ->addColumn(['data'=>'user','name'=>'user','title'=>'User'])
            ->addColumn(['data'=>'usaha_o_merchant','name'=>'usaha_o_merchant','title'=>'Nama Usaha'])
            ->addColumn(['data'=>'type','name'=>'type','title'=>'Type'])
            ->addColumn(['data'=>'jumlah','name'=>'jumlah','title'=>'Jumlah'])
            ->addColumn(['data'=>'waktu','name'=>'waktu','title'=>'Waktu'])
            ->addColumn(['data'=>'action','name'=>'action','title'=>'Action','orderable'=>false,'searchable'=>false]);

        return view('administrator.tabungan.index')->with(compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('administrator.tabungan.create');
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
            'o_merchant_id'         => 'required|exists:o_merchants,id',
            'usaha_o_merchant_id'   => 'required|exists:usaha_o_merchants,id',
            'jumlah'                => 'required|numeric',
        ];

        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'exists'    => 'Field :attribute tidak ditemukan.',
            'numeric'   => 'Field :attribute harus berupa angka.',
        ];

        $this->validate($request, $rules, $messages);
        
        DB::beginTransaction();

        $data = Tabungan::create(array_merge($request->all(),[
            'type' => 'Deposit'
        ]));

        $usaha = UsahaOMerchant::with('o_merchant.user')->where('id', $request->get('usaha_o_merchant_id'))->first();
        // $cashobbi = CashObbi::create(array_merge($request->all(), [
        //     'tipe' => 'Tabungan',
        //     'kode' => $om->kode,
        //     'user_id' => $om->user->id,
        //     'cash' => 1,
        //     'status' => 'Masuk'
        // ]));
        // $cashobbi->save()

        if($data->save()){

            DB::commit();

            Session::flash("flash_notification",[
                "level"=>"success",
                "message"=>"Tabungan for " . $usaha->o_merchant->user->fullname . " successfully saved."
            ]);
        }else{
            DB::rollBack();

            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Data failed to be saved."
            ]);
        }
        return redirect()->route('tabungan.index');
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
        //
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
        //
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

        $data = Tabungan::with('usaha_o_merchant.o_merchant.user')->find($id);
        $nama = $data->usaha_o_merchant->o_merchant->user->fullname;
        // $cashobbi = CashObbi::where('tipe', 'Tabungan')
        //     ->where('kode', $om->kode)
        //     ->where('user_id', $om->user_id)
        //     ->where('created_at', '=', Carbon::createFromFormat('Y-m-d H:i:s', $data->created_at)->format('Y-m-d H:i:s'))
        //     ->first();
        // !$cashobbi->delete()

        // TODO : Check Data already used
        // $cek = Donasi::where('updated_at', '>', $data->updated_at)->count();
        // if($cek > 0){
        //     DB::rollBack();

        //     Session::flash("flash_notification",[
        //         "level"=>"warning",
        //         "message"=>"Data already used."
        //     ]);
        //     return redirect()->back();
        // }

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
            "message"=>"Tabungan for " . $nama . " successfully deleted."
        ]);
        
        DB::commit();

        return redirect()->route('tabungan.index');
    }

    public function usahaomerchant($id)
    {
        $result = array(); $list = [];

        $datas = UsahaOMerchant::with('usaha')->where('o_merchant_id',$id)->get();
        if($datas){
            foreach ($datas as $data) {
                array_push($list,[
                    'id'=>''.$data->id,
                    'nama'=>$data->usaha->nama_usaha
                ]);
            }
            $result['hasil'] = $list;
        }else{
            $result['hasil'] = $list;
        }
        return json_encode($result);
    }
}
