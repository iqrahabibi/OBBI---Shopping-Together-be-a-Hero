<?php

namespace App\Http\Controllers\API\Belanja;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helper\Form as FM;
use App\OBBI\obbiHelper as DATA;
use Illuminate\Support\Facades\Mail;

use App\Model\Checkout;
use App\Model\User;
use App\Model\UserAlamat;
use App\Model\CartOnline;
use App\Model\CartOnlineDetailNasional;
use App\Model\CartOnlineDetailDaerah;
use App\Model\UsahaOMerchant;
use App\Model\Saldo;
use App\Model\DigiPay;
use App\Model\Finance;
use App\Model\Kurir;
use Carbon\Carbon;

use DB;
use Auth;

class Pembayaran extends Controller
{
    public function __invoke(Request $request){
        (new FM)->required($request,[
            'alamat_id','cart_id','tipe_pembayaran','tipe_belanja',
            'detil_kirim'
        ]);

        $harga_kirim    = 0;
        $detil_kirim    = $request->detil_kirim;

        $kode       = rand(000,999);
        
        if(strlen($kode) < 3)
        {
            $kode = $kode."".rand(0,9);
        }

        while (!empty(Checkout::where('kode',$kode)->first())) {
            $kode   = rand(000,999);

            if(strlen($kode) < 3)
            {
                $kode = $kode."".rand(0,9);
            }
        }

        if($request->tipe_belanja == 'nasional'){
            foreach($detil_kirim as $key => $value){
                $cek_harga_kirim = Kurir::where('id',$value['kurir_id'])->first();

                $harga_kirim+= $cek_harga_kirim->value;
            }
        }else if($request->tipe_belanja == 'lokal'){
            if(!empty($request->detil_kirim)){

                foreach($detil_kirim as $key => $value){
                    if($value['kurir_id'] == 2){
                        $harga_kirim+=10000;
                    }
                }
            }
        }

        DB::beginTransaction();

        $cek_user_alamat = UserAlamat::with('kelurahan.kecamatan')->findOrFail($request->alamat_id);

        if($request->tipe_belanja == 'lokal'){
            $cek_cart_id    = CartOnline::with('detail_daerah.usaha_om.usaha.kelurahan.kecamatan')
            ->findOrFail($request->cart_id);

            foreach($cek_cart_id->detail_daerah as $key => $value){
                if($cek_user_alamat->kelurahan->kecamatan->id != $value->usaha_om->usaha->kelurahan->kecamatan->id){
                    throw new \SecurityExceptions('Cuy kejauhan, OTW cuma sampai kecamatan saja.');
                }
            }

        }

        $expired_at     = Carbon::now('Asia/Jakarta')->addDays(1);

        $cek_saldo  = Saldo::where('user_id',$request->user_id)->first();
        $keuntungan = 0;
        $amal       = 0;

        if($request->tipe_pembayaran == 'doi'){
            
            $invoice    = DATA::auto_invoice_checkout('checkouts','invoice','TRXID');

            if($request->tipe_belanja == "lokal"){
                $cari_total_cart = CartOnline::with('detail_daerah.barang')->where('id',$request->cart_id)->first();

                if(!emty($cari_total_cart->detail_daerah->barang->keuntungan)){

                    $keuntungan     = $cari_total_cart->detail_daerah->barang->keuntungan;
                }

                if(!empty($cari_total_cart->detail_daerah->barang->jumlah_amal)){

                    $amal           = $cari_total_cart->detail_daerah->barang->jumlah_amal;
                }

                $total_belanja  = $cari_total_cart->total_cart + $request->harga_kirim + $kode 
                                + $keuntungan + $amal;

            }else if($request->tipe_belanja == "nasional"){

                if(!emty($cari_total_cart->detail_nasional->barang->keuntungan)){

                    $keuntungan     = $cari_total_cart->detail_nasional->barang->keuntungan;
                }

                if(!empty($cari_total_cart->detail_nasional->barang->jumlah_amal)){

                    $amal           = $cari_total_cart->detail_nasional->barang->jumlah_amal;
                }

                $cari_total_cart = CartOnline::with('detail_nasional.barang')->where('id',$request->cart_id)->first();
                $total_belanja  = $cari_total_cart->total_cart + $request->harga_kirim + $kode 
                                + $keuntungan + $amal;
            }

            if($total_belanja > $cek_saldo->saldo){
                throw new \SecurityExceptions('Saldo anda tidak mencukupi.');
            }

            $digipay    = DigiPay::create(array_merge($request->all(),[
                'user_id'      => $request->user_id,
                'awal'         => $cek_saldo->saldo,
                'finance_id'   => 999,
                'jumlah'       => $total_belanja,
                'akhir'        => $cek_saldo->saldo - $total_belanja,
                'notes'        => strtoupper('Belanja barang'),
                'kode'         => 0,
                'valid'        => 1,
                'trxid'        => $invoice,
            ]));
    
            $create     = Checkout::create(array_merge($request->all(),[
                'status'        => 'waiting',
                'invoice'       => $invoice,
                'expired_at'    => $expired_at,
                'total_belanja' => $total_belanja,
                'kode'          => $kode,
                'harga_kirim'   => $harga_kirim,
                'amal'          => $cari_total_cart->detail_nasional->barang->amal,
                'keuntungan'    => $cari_total_cart->detail_nasional->barang->keuntungan,
            ]));

            $total_amal = $cari_total_cart->detail_nasional->barang->amal + $cek_saldo->amal;
            $total_keuntungan = $cari_total_cart->detail_nasional->barang->keuntungan + $cek_saldo->keuntungan;

            $saldo    = Saldo::updateOrCreate(
                ['user_id' => $request->user_id],
                ['saldo' => $digipay->akhir+$kode,'amal' => $total_amal,'keuntungan' => $total_keuntungan]
            );
            
            if($request->tipe_belanja == "nasional"){

                $update = CartOnlineDetailNasional::where('cart_id',$create->cart_id)
                ->update(['detail_kirim' => json_encode($request->detil_kirim)]);
            }else if($request->tipe_belanja == "lokal"){
                $update = CartOnlineDetailDaerah::where('cart_id',$create->cart_id)
                ->update(['detail_kirim' => json_encode($request->detil_kirim)]);
            }
    
            if($create->save()){

                $update_checkout = Checkout::where('id',$create->id)->update(['kode' => 0]);
                $update = CartOnline::where('id',$request->cart_id)->update(['status' => 'done']);

                DB::commit();
    
                $user   = User::where('id',$request->user_id)->first();
                
                if($request->tipe_belanja == 'lokal'){
                    $data_barang = Checkout::with('cart','alamat','cart.detail_daerah','cart.om_barang_inventory')->where('id',$create->id)->first();
                }else if($request->tipe_belanja == 'nasional'){
                    $data_barang = Checkout::with('cart','alamat','cart.detail_nasional','cart.barang_inventory')->where('id',$create->id)->first();
                }
    
                // Mail::send('auth.email.checkout',compact('user','data_barang'),function($m) use ($user){
                //     $m->to($user->email,$user->fullname)->subject('Invoice Belanja');
                // });
    
                $success['code']    = 200;
                $success['message'] = "Berhasil melakukan transaksi.";
    
                return response()->json(['meta' => $success]);
            }else{
                DB::rollBack();
    
                $success['code']    = 400;
                $success['message'] = "Error pada query.";
    
                return response()->json(['meta' => $success]);
            }
        }else if($request->tipe_pembayaran == 'transfer'){
    
            $invoice    = DATA::auto_invoice_checkout('checkouts','invoice','TRXID');
            

            if($request->tipe_belanja == "lokal"){
                $cari_total_cart = CartOnline::with('detail_daerah.barang')->where('id',$request->cart_id)->first();

                if(!empty($cari_total_cart->detail_daerah->barang->keuntungan)){

                    $keuntungan     = $cari_total_cart->detail_daerah->barang->keuntungan;
                }

                if(!empty($cari_total_cart->detail_daerah->barang->jumlah_amal)){

                    $amal           = $cari_total_cart->detail_daerah->barang->jumlah_amal;
                }

                $total_belanja  = $cari_total_cart->total_cart + $request->harga_kirim + $kode 
                                + $keuntungan + $amal;
                                
            }else if($request->tipe_belanja == "nasional"){
                $cari_total_cart = CartOnline::with('detail_nasional.barang')->where('id',$request->cart_id)->first();

                if(!empty($cari_total_cart->detail_nasional->barang->keuntungan)){

                    $keuntungan     = $cari_total_cart->detail_nasional->barang->keuntungan;
                }

                if(!empty($cari_total_cart->detail_nasional->barang->jumlah_amal)){

                    $amal           = $cari_total_cart->detail_nasional->barang->jumlah_amal;
                }

                $total_belanja  = $cari_total_cart->total_cart + $request->harga_kirim + $kode 
                                + $keuntungan + $amal;
            }
    
            $create     = Checkout::create(array_merge($request->all(),[
                'status'        => 'waiting',
                'invoice'       => $invoice,
                'expired_at'    => $expired_at,
                'kode'          => $kode,
                'total_belanja' => $total_belanja,
                'harga_kirim'   => $harga_kirim,
                'amal'          => $cari_total_cart->detail_nasional->barang->amal,
                'keuntungan'    => $cari_total_cart->detail_nasional->barang->keuntungan,
            ]));
                
            if($request->tipe_belanja == "lokal"){
                $update = CartOnlineDetailDaerah::where('cart_id',$create->cart_id)->update(['detail_kirim' => json_encode($request->detil_kirim)]);
            }else if($request->tipe_belanja == "nasional"){
                $update = CartOnlineDetailNasional::where('cart_id',$create->cart_id)->update(['detail_kirim' => json_encode($request->detil_kirim)]);
            }
    
            if($create->save()){
                
                // $update_checkout = Checkout::where('id',$create->id)->update(['kode' => 0]);
                $update = CartOnline::where('id',$request->cart_id)->update(['status' => 'done']);
            
    
                $user   = User::where('id',$request->user_id)->first();
                
                if($request->tipe_belanja == 'lokal'){
                    $data_barang = Checkout::with('cart','alamat','cart.detail_daerah','cart.om_barang_inventory')->where('id',$create->id)->first();
                    $data['show_invoice']   = 
                    array(
                        "expired_at"    => $data_barang->expired_at,
                        "invoice"       => $data_barang->invoice,
                        "status"        => $data_barang->status, 
                        "alamat"        => $data_barang->alamat,
                        "tipe_pembayaran" => $data_barang->tipe_pembayaran,
                        "total_belanja" => $data_barang->total_belanja,
                        "phone"         => $data_barang->alamat->phone,
                    );
                }else{
                    $data_barang = Checkout::with('cart','alamat.kelurahan.kecamatan.kota.provinsi','cart.detail_nasional','cart.barang_inventory')
                    ->where('id',$create->id)->first();
                    $data['show_invoice']   = 
                    array(
                        "expired_at"    => $data_barang->expired_at,
                        "invoice"       => $data_barang->invoice,
                        "status"        => $data_barang->status, 
                        "alamat"        => $data_barang->alamat,
                        "tipe_pembayaran" => $data_barang->tipe_pembayaran,
                        "total_belanja" => $data_barang->total_belanja,
                        "phone"         => $data_barang->alamat->phone,
                    );
                }
    
                // Mail::send('auth.email.checkout',compact('user','data_barang'),function($m) use ($user){
                //     $m->to($user->email,$user->fullname)->subject('Invoice Belanja');
                // });

                DB::commit();
    
                $success['code']    = 200;
                $success['message'] = "Silahkan lakukan pembayaran.";
    
                return response()->json(['meta' => $success,'data' => $data]);
            }else{
                DB::rollBack();
    
                $success['code']    = 400;
                $success['message'] = "Error pada query.";
    
                return response()->json(['meta' => $success]);
            }
        }

    }
}
