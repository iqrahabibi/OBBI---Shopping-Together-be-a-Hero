<?php

namespace App\Http\Controllers\API\Bakoel\Multifinance;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use App\Helper\Form as FM;

use App\Model\DigiPay;
use App\Model\Saldo;
use App\Model\User;
use App\Model\Finance;

use DB;

class Payment extends Controller
{
    public function __invoke(Request $request){
        (new FM)->required($request,[
            'refID','total','user_id','product_code'
        ]);

        $balance    = Saldo::where('user_id',$request->user_id)->first();

        $refId  = $request->refID;
        $total  = $request->total;

        if($balance->saldo < $total)
        {
            $success['code']    = 300;
            $success['message'] = "Saldo anda tidak mencukupi, silahkan lakukan pengisian ulang.";

            return response()->json(['meta' => $success]);
        }

        if($balance->saldo < 0 || $balance->saldo == 0)
        {
            $success['code']    = 300;
            $success['message'] = "Saldo anda tidak mencukupi, silahkan lakukan pengisian ulang.";

            return response()->json(['meta' => $success]);
        }

        $client = new Client();

        $response   = $client->request('POST',config("app.url_multifinance")."multifinance/payment.php",[
            "form_params" => [
                "refID" => $refId,
                "product_code"  => $request->product_code,
                "total_bayar" => $total,
            ]
        ]);

        $responses[]  = json_decode($response->getBody()->getContents(),true);

        $data   = [];

        $cost   = Finance::where('kode',$request->product_code)->where('valid',1)->first();
        $total  = $total - ($cost->amal + $cost->keuntungan);

        DB::beginTransaction();

        try
        {
            foreach($responses as $key => $value)
            {
                if($value['responseCode'] == 00)
                {
                    $insert = new DigiPay();
                    $insert->user_id    = $request->user_id;
                    $insert->jumlah     = $total;
                    $insert->finance_id = $cost->id;
                    $insert->awal       = $balance->saldo;
                    $insert->akhir      = $balance->saldo - ($total+$cost->amal+$cost->keuntungan);
                    $insert->notes      = strtoupper("pembayaran Multifinance.");
                    $insert->kode       = 0;
                    $insert->valid      = 1;
                    $insert->trxid      = $value['nomorKontrak'];

                    if(!empty($request->input('phone'))){
                        $insert->phone     = $request->phone;
                    }

                    $insert->save();

                    $total_amal = $cost->amal + $balance->amal;
                    $total_keuntungan = $cost->keuntungan + $balance->keuntungan;

                    $saldo    = Saldo::updateOrCreate(
                        ['user_id' => $request->user_id],
                        ['saldo' => $insert->akhir,'amal' => $total_amal,'keuntungan' => $total_keuntungan]
                    );
                    
                    DB::commit();

                    $user   = User::where('id',$request->user_id)->first();

                    // Mail::send('auth.email.multifinance',compact('insert','user','responses'),function($m) use ($user){
                    //     $m->to($user->email,$user->fullname)->subject('[OBBI Application] Notification Pembayaran PDAM');
                    // });

                    $data['access_token']   = array('token'=>$request->header('Authorization'));

                    $data['multifinance']   = $value;

                    $success['code'] = 200;
                    $success['message']= $value['message'];

                    return response()->json(['meta' => $success,'data' => $data]);
                    
                }else{
                    DB::rollBack();

                    $success['code'] = $value['responseCode'];
                    $success['message']= $value['message'];
        
                    return response()->json(['meta' => $success]);
                }
            }
        }catch(QueryException $e)
        {
            DB::rollBack();
            
            $success['code']        = 401;
            $success['message']     = $e->getMessage();
            
            return response()->json(['meta' => $success]);
        }
    }
}
