<?php

namespace App\Http\Controllers\API\Bakoel\PLN\Token;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use GuzzleHttp\Client;
use App\Helper\Form as FM;

use App\Model\DigiPay;
use App\Model\Saldo;
use App\Model\Finance;
use App\Model\User;

use DB;

class Manual_advice extends Controller
{
    public function __invoke(Request $request){
        (new FM)->required($request,[
            'hasId','user_id'
        ]);

        $client = new Client();

        $response   = $client->request('GET',config('app.url')."/pln/token/manualadvice.php?manualAdviceHashID=".$request->hasId);

        $responses[]= json_decode($response->getBody()->getContents(),true);

        $insert    = DigiPay::where('trxid',$request->hasId)->first();

        DB::beginTransaction();

        try
        {
            foreach($responses as $key => $value)
            {

                if($value['responseCode'] == 00)
                {
                    $number = $value['data']['tokenNumber'];
                    $array  = array_map('intval',str_split($number));
                    $jml    = count($array);
                    $j      = 1;
                    $content = "";
        
                    for($i = 0 ; $i < $jml ; $i++)
                    {
                        $string = "";
                        if($j%4 == 0)
                        {
                            $string = $array[$i]." ".$string;
                            $j = 0;
                        }else{
                            $string = $array[$i].$string;
                        }
        
                        $string2['angka'][] = $string;
                        $j++;
                    }
        
                    $g = str_replace(",","",str_replace("]","",str_replace("[",'',str_replace('"','',json_encode($string2['angka'])))));

                    if($insert->token_type == 1)
                    {
                        $content = "Pembelian Token Listrik DENOM";
                    }else
                    {
                        $content = "Pembelian Token Listrik UNSOLD";
                    }

                    $insert->notes  = strtoupper($content);
                    $insert->valid  = 1;
                    $insert->code   = 0;
                    $insert->trxid  = $value['data']['ref'];

                    $insert->save();

                    DB::commit();

                    $user   = User::where('id',$insert->user_id)->first();

                    Mail::send('auth.email.plntoken',compact('insert','user','responses','g'),function($m) use ($user){
                        $m->to($user->email,$user->fullname)->subject('[OBBI Application] Notification Pembayaran Listrik Token');
                    });

                    $data['access_token']   = array('token'=>$request->header('Authorization'));

                    $data['prepaid']   = array('data' => $value['data'],"totalTagihan" => $value['totalTagihan'],"infotext" => $value['infotext']);

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
