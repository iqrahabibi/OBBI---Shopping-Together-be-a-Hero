<?php

namespace App\Http\Controllers\Administrator;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Builder;

use App\Model\Checkout;
use App\Model\CartOnlineDetailDaerah;
use App\Model\CartOnline;

use DataTables;
use Session;
use Auth;

class CheckoutDataDaerahController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Builder $htmlBuilder)
    {
        if($request->ajax()){
            $datas = Checkout::with('cart','alamat','cart.om_barang_inventory')->where('tipe_belanja','lokal')->get();
            
            return Datatables::of($datas)
            ->addColumn('alamat',function($data){
                return $data->alamat->alamat;
            })
            ->addColumn('barang',function($data){
                
                return $data->cart->om_barang_inventory->barang->nama_barang;
            })
            ->addColumn('qty',function($data){
                
                return $data->cart->total_qty;
            })
            ->addColumn('total_cart',function($data){
                
                return $data->cart->total_belanja;
            })
            ->addColumn('action',function($data){

                if($data->status == 'waiting'){
                    return view('administrator.transaksi.daerah._action',[
                        'model' =>$data,
                        'ubah_status' =>route('checkoutdaerah.edit',$data->id),
                        'form_url' => route('checkoutdaerah.destroy',$data->id),
                        'confirm_message'=>'Yakin mau cancel pesanan '.$data->cart->om_barang_inventory->barang->nama_barang.' ?'
                    ]);
                }

                return 'No Action';
            })
            ->make(true);
        }

        $html=$htmlBuilder
            ->addColumn(['data'=>'invoice','name'=>'invoice','title'=>'Invoice'])
            ->addColumn(['data'=>'barang','name'=>'barang','title'=>'Barang'])
            ->addColumn(['data'=>'alamat','name'=>'alamat','title'=>'Alamat'])
            ->addColumn(['data'=>'total_cart','name'=>'total_cart','title'=>'Total Cart'])
            ->addColumn(['data'=>'qty','name'=>'qty','title'=>'Quantity'])
            ->addColumn(['data'=>'harga_kirim','name'=>'harga_kirim','title'=>'Harga Kirim'])
            ->addColumn(['data'=>'total_belanja','name'=>'total_belanja','title'=>'Total Belanja'])
            ->addColumn(['data'=>'tipe_belanja','name'=>'tipe_belanja','title'=>'Tipe Belanja'])
            ->addColumn(['data'=>'status','name'=>'status','title'=>'status'])
            ->addColumn(['data'=>'action','name'=>'action','title'=>'Action','orderable'=>false,'searchable'=>false]);

        return view('administrator.transaksi.daerah.index')->with(compact('html'));
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

        $find   = Checkout::findOrFail($id);

        $find->status   = 'done';

        $update_detail_daerah = CartOnlineDetailDaerah::where('cart_id',$find->cart_id)->update(['status' => 'pending']);


        if($find->update()){
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

        return redirect()->route('checkoutdaerah.index');
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

        $find   = Checkout::findOrFail($id);

        $find->status   = 'cancelled';

        $cart_detail_daerah = CartOnlineDetailDaerah::where('cart_id',$find->cart_id)->update(['status' => 'cancelled']);
        $cart_online = CartOnline::where('id',$find->cart_id)->update(['status' => 'cancelled']);

        if($find->update()){
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

        return redirect()->route('checkoutdaerah.index');
    }
}
