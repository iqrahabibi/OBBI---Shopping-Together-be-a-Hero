<?php

namespace App\Http\Controllers\API\Saldo;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\OBBI\obbiHelper as DATA;
use App\Helper\Form as FM;

use App\Model\User;
use App\Model\DigiPay;
use App\Model\Saldo;

use DB;

class Balance extends Controller {
    public function __invoke (Request $request) {
        (new FM)->required($request, [
            'user_id', 'saldo'
        ]);

        $user_id = $request->user_id;
        $saldo = $request->saldo;

        $kode = rand(000, 999);

        if ( strlen($kode) < 3 ) {
            $kode = $kode . "" . rand(0, 9);
        }

        while ( !empty(DigiPay::where('kode', $kode)
                              ->first()) ) {
            $kode = rand(000, 999);

            if ( strlen($kode) < 3 ) {
                $kode = $kode . "" . rand(0, 9);
            }
        }

        $jumlah = $kode + $saldo;

        if ( !empty($request->input('notes')) ) {
            $notes = $request->notes;
        } else {
            $notes = "";
        }

        if ( $saldo > 2000000 ) {
            return ["meta"=>["code"=>500, "message"=>"Saldo tidak boleh lebih dari Rp2.000.000"]];
            //throw new \DataErrorExceptions('Saldo tidak boleh lebih dari Rp2.000.000', 'saldo');
        }

        DB::beginTransaction();

        $invoice = DATA::invoice('digi_pays', 'id', 'PBL-');
        $email = User::where('id', $user_id)
                     ->first();

        $awal = Saldo::where('user_id', $user_id)
                     ->first();

        if ( empty($awal) ) {
            $awal['saldo'] = 0;
        }

        if ( empty($email) ) {
            return ["meta"=>["code"=>500, "message"=>"Data tidak ditemukan."]];
            //throw new \DataNotFoundExceptions('Data tidak ditemukan.', 'user');
        }

        $digi_pays = new DigiPay();
        $digi_pays->user_id = $user_id;
        $digi_pays->invoice = $invoice;
        $digi_pays->awal = $awal['saldo'];
        $digi_pays->jumlah = $saldo + $kode;
        $digi_pays->akhir = $awal['saldo'] + $saldo + $kode;
        $digi_pays->notes = $notes;
        $digi_pays->kode = $kode;
        $digi_pays->valid = 0;

        $digi_pays->save();

        DB::commit();

        Mail::send('auth.email.billingsaldo', compact('email', 'jumlah', 'kode', 'digi_pays', 'invoice'), function ($m) use ($email) {
            $m->to($email->email, $email->fullname)
              ->subject('[OBBI Application] Notification Pembelian Saldo');
        });

        $content = "Ada pembelian saldo OBBI sebesar " . number_format($jumlah, 0) . " dari user: " . $email->fullname . " pada tanggal: " . date('d M Y H:i:s', strtotime($digi_pays->created_at));

        Mail::send('bodyemail', compact('content', 'email', 'invoice'), function ($m) use ($content, $email) {
            $m->to('jokopriyono0201@gmail.com', 'Joko')
            
              ->subject('Notification Pembelian Saldo');
        });

        return (new \Data)->respond([
            'access_token' => [ 'token' => $request->header('Authorization') ],
            'saldo'        => $digi_pays
        ]);

    }
}
