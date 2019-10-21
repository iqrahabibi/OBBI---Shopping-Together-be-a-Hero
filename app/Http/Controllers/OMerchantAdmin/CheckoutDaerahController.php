<?php

namespace App\Http\Controllers\OMerchantAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Builder;

use App\Model\CartOnlineDetailDaerah;
use App\Model\User;
use App\Model\UsahaOmerchant;
use App\Model\OMerchantAdmin;

use DataTables;
use Session;
use Auth;

class CheckoutDaerahController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Builder $htmlBuilder)
    {

        $om_admin   = OMerchantAdmin::where('user_id',Auth::user()->id)->first();

        if($request->ajax()){
            $datas = CartOnlineDetailDaerah::with('cart','barang','usaha_om','cart.om_barang_inventory','usaha_om.usaha')
            ->whereNotIn('status',['waiting','cancelled'])
            ->where('kode',$om_admin->kode)
            ->get();
            
            return Datatables::of($datas)
            ->addColumn('barang',function($data){
                
                return $data->barang->nama_barang;
            })
            ->addColumn('usaha',function($data){
                
                return $data->usaha_om->usaha->nama_usaha;
            })
            ->addColumn('total_cart',function($data){
                
                return $data->cart->total_belanja;
            })
            ->addColumn('action',function($data){

                if($data->status == 'pending'){
                    return view('omerchantadmin.checkoutomerchant._action',[
                        'model' =>$data,
                        'proccess' =>route('transaksidaerah.edit',$data->id),
                        'confirm_message'=>'Yakin mau proses pesanan '.$data->barang->nama_barang.' ?',
                        'tipe' => 1
                    ]);
                }else if($data->status == 'proccessed'){
                    return view('omerchantadmin.checkoutomerchant._action',[
                        'model' =>$data,
                        'sending' =>route('transaksidaerah.destroy',$data->id),
                        'confirm_message'=>'Yakin mau sending pesanan '.$data->barang->nama_barang.' ?',
                        'tipe' => 2
                    ]);
                }else if($data->status == 'sending'){
                    return view('omerchantadmin.checkoutomerchant._action',[
                        'model' =>$data,
                        'sending' =>route('transaksidaerah.sending',$data->id),
                        'confirm_message'=>'Yakin mau sent pesanan '.$data->barang->nama_barang.' ?',
                        'tipe' => 3
                    ]);
                }

                return 'No Action';
            })
            ->make(true);
        }

        $html=$htmlBuilder
            ->addColumn(['data'=>'usaha','name'=>'usaha','title'=>'Nama Usaha'])
            ->addColumn(['data'=>'barang','name'=>'barang','title'=>'Barang'])
            ->addColumn(['data'=>'qty','name'=>'qty','title'=>'Quantity'])
            ->addColumn(['data'=>'harga','name'=>'harga','title'=>'Harga'])
            ->addColumn(['data'=>'total_cart','name'=>'total_cart','title'=>'Total Belanja'])
            ->addColumn(['data'=>'status','name'=>'status','title'=>'status'])
            ->addColumn(['data'=>'action','name'=>'action','title'=>'Action','orderable'=>false,'searchable'=>false]);

        return view('omerchantadmin.checkoutomerchant.index')->with(compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        DB::beginTransaction();

        $update     = CartOnlineDetailDaerah::findOrFail($id);

        $update->status = 'proccessed';

        if($update->update()){
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

        return redirect()->route('transaksidaerah.index');
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
    public function destroy($id)
    {
        DB::beginTransaction();

        $update     = CartOnlineDetailDaerah::findOrFail($id);

        $update->status = 'sending';

        if($update->update()){
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

        return redirect()->route('transaksidaerah.index');
    }

    public function sending($id){
        DB::beginTransaction();

        $update     = CartOnlineDetailDaerah::findOrFail($id);

        $update->status = 'sent';

        if($update->update()){
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

        return redirect()->route('transaksidaerah.index');
    }
}
