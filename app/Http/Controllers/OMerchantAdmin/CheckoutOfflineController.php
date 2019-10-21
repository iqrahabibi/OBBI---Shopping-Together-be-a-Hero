<?php

namespace App\Http\Controllers\OMerchantAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\OBBI\obbiHelper as DATA;

use App\Model\CheckoutOffline;
use App\Model\OMerchantBarangGrosir;
use App\Model\CartOnline;
use App\Model\CartOnlineDetailDaerah;
use App\Model\OMerchantAdmin;
use App\Model\OMerchantBarangInventory;
use App\Model\Barang;

use DB;
use Auth;
use Session;

class CheckoutOfflineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('omerchantadmin.offline.kasir.index');
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

        DB::beginTransaction();

        $om_admin   = OMerchantAdmin::where('user_id',Auth::user()->id)->first();

        if(empty($request->total_belanja)){
            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Total belanja tidak boleh kosong."
            ]);

            return redirect()->back();
        }
        
        $total_belanja  = $request->total_belanja;
        $total          = 0;
        $flag           = 0;
        $total_qty      = 0;
        
        $insert_cart    = CartOnline::create(array_merge($request->all(),[
            'total_belanja' => $total_belanja,
            'total_qty'     => 0,
            'status'        => 'done',
            'belanja_id'    => 0,
            'user_id'       => Auth::user()->id,
            'kode_invoice'  => DATA::auto_invoice_cart('cart_onlines','id','COD')
        ]));

        foreach($request->barang as $key => $value){
            $total_qty+=$request->qty[$key];
            $total+=$request->total_harga[$key];

            $cek_barang = OMerchantBarangGrosir::where('id',$request->barang[$key])->first();

            $cek_belanja_id = OMerchantBarangInventory::where('barang_id',$cek_barang->barang_id)->first();

            $insert_cart->belanja_id    = $cek_belanja_id->id;

            $data   = CartOnlineDetailDaerah::updateOrCreate(
                ['cart_id' => $insert_cart->id,'kode' => $om_admin->kode,'barang_id' => $cek_barang->barang_id,'status' => 'received'],
                ['varian' => $request->varian[$key],'harga' > $request->total_harga[$key],'qty' => $request->qty[$key]]
            );
        }

        $insert_cart->total_qty     = $total_qty;

        if($total_belanja != $total){
            Session::flash("flash_notification",[
                "level"=>"warning",
                "message"=>"Total belanja tidak valid."
            ]);

            return redirect()->back();
        }else{
            $inser_checkout_offline = new CheckoutOffline();
            $inser_checkout_offline->cart_id    = $insert_cart->id;
            $inser_checkout_offline->invoice    = DATA::auto_invoice_cart('checkout_offlines','id','COF');
            $inser_checkout_offline->total_belanja = $request->total_belanja;
            $inser_checkout_offline->status     = "done";

            $inser_checkout_offline->save();

            if($insert_cart->save()){
                DB::commit();

                Session::flash("flash_notification",[
                    "level"=>"success",
                    "message"=>"Data successfull saved."
                ]);

                return redirect()->route('kasir.index');
            }else{
                DB::rollBack();

                Session::flash("flash_notification",[
                    "level"=>"success",
                    "message"=>"Data failed saved."
                ]);

                return redirect()->route('kasir.index');
            }

        }



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
    public function destroy($id)
    {
        //
    }

    public function show_harga_satuan(Request $request){
        DB::beginTransaction();

        $kode_usaha = OMerchantAdmin::where('user_id',Auth::user()->id)->first();

        $data   = OMerchantBarangGrosir::with('barang','varian')->where([
            ['id','=',$request->id],
            ['kode_usaha','=',$kode_usaha->kode]
        ])->first();
        
        if(!empty($data)){
            $array['belanja_id']    = $data->id;
            $array['harga_jual']    = $data->harga_jual;
            $array['varian']        = $data->varian->varian_barang;
            
            return $array;
        }else{
            $array['belanja_id']    = 0;
            $array['harga_jual']    = 0;
            $array['varian']        = '';

            return $array;
        }
    }

    public function show_barang(Request $request){
        DB::beginTransaction();

        $kode_usaha = OMerchantAdmin::where('user_id',Auth::user()->id)->first();

        $cek_barang = Barang::where('sku',$request->id)->first();
        
        $result = array(); $list = [];

        if(empty($cek_barang)){
            $result['hasil'] = $list;
            return json_encode($result);
        }

        $datas = OMerchantBarangGrosir::with('barang','varian')->where([
            ['barang_id','=',$cek_barang->id],
            ['kode_usaha','=',$kode_usaha->kode]
        ])->get();

        if($datas){
            foreach ($datas as $data) {
                array_push($list,[
                    'id'=>''.$data->id,
                    'nama_barang'=>$data->barang->nama_barang.' - '.$data->varian->varian_barang.' - '.$data->harga_jual
                ]);
            }
            $result['hasil'] = $list;
        }else{
            $result['hasil'] = $list;
        }
        return json_encode($result);
    }
}
