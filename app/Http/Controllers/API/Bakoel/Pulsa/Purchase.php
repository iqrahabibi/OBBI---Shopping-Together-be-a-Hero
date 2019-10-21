<?php

namespace App\Http\Controllers\API\Bakoel\Pulsa;

use App\Http\Controllers\Controller;
use App\Model\Finance;
// use App\Jobs\PulsaTransaksiJob;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Helper\Form as FM;

use App\Model\User;
use DB;

class Purchase extends Controller
{
    public function __invoke(Request $request)
    {
        //return ['meta' => ['code' => 500, 'message' => 'Fitur ini masih dalam tahap maintenance']];
        (new FM)->required($request, [
            'code', 'purchase', 'msisdn',
            'purchase_amount', 'product_id',
        ]);

        $cek_saldo = Saldo::where('user_id', $request->user_id)
            ->first();
        $cost = Finance::where('kode', $request->code)
            ->where('valid', 1)
            ->first();

        $purchase = $request->purchase;

        if ($cek_saldo->saldo < $purchase) {
            $success['code'] = 300;
            $success['message'] = "Saldo anda tidak mencukupi, silahkan lakukan pengisian ulang.";

            return response()->json(['meta' => $success]);
        } else if ($cek_saldo->saldo < 0) {
            $success['code'] = 300;
            $success['message'] = "Saldo anda tidak mencukupi, silahkan lakukan pengisian ulang.";

            return response()->json(['meta' => $success]);
        }

        $client = new Client();

        $response = $client->request('POST', config('app.url') . "/pulsa/purchase.php", [
            'form_params' => [
                'product_id' => $request->product_id,
                'msisdn' => $request->msisdn,
                'purchase_amount' => $request->purchase_amount,
            ],
        ]);

        $responses = json_decode($response->getBody()
                ->getContents(), true);

        $total_amal = 0;
        $total_keuntungan = 0;

        $notes = "Pembelian voucher pulsa.";

        if ($request->code == "PDT") {
            $notes = "Pembelian paket internet.";
        }

        if ($responses['meta']['code'] == 200) {
            DB::beginTransaction();

            try {
                $digi_pay = new DigiPay();
                $digi_pay->user_id = $request->user_id;
                $digi_pay->awal = $cek_saldo->saldo;
                $digi_pay->finance_id = $cost->id;
                $digi_pay->jumlah = $purchase;
                $digi_pay->akhir = $cek_saldo->saldo - $purchase;
                $digi_pay->notes = strtoupper($notes);
                $digi_pay->kode = 0;
                $digi_pay->valid = 1;
                $digi_pay->trxid = $responses['purchase']['trxID'];
                $digi_pay->phone = $request->msisdn;

                $digi_pay->save();

                $total_amal = $cost->amal + $cek_saldo->amal;
                $total_keuntungan = $cost->keuntungan + $cek_saldo->keuntungan;

                $saldo = Saldo::updateOrCreate(
                    ['user_id' => $request->user_id],
                    ['saldo' => $digi_pay->akhir, 'amal' => $total_amal, 'keuntungan' => $total_keuntungan]
                );

                DB::commit();

                // PulsaTransaksiJob::dispatch($digi_pay);

                $user = User::where('id', $request->user_id)
                    ->first();

                Mail::send('auth.email.billingpulsa', compact('digi_pay', 'user'), function ($m) use ($user) {
                    $m->to($user->email, $user->fullname)
                        ->subject('[OBBI Application] Notification Pembelian Pulsa/Paket Data');
                });
                $success['code'] = 200;

                return response()->json(['meta' => $success, 'data' => $responses['purchase']]);
            } catch (QueryException $e) {
                DB::rollBack();

                $success['code'] = 400;
                $success['message'] = $e->getMessage();

                return response()->json(['meta' => $success]);
            }

        } else {
            return response()->json($responses);
        }
    }
}
