<?php

namespace App\Http\Controllers\API\Bakoel\Pulsa\Paket;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Helper\Form as FM;
use App\Model\User;
use App\Model\Finance;
use DB;


class Read extends Controller
{
    public function __invoke(Request $request)
    {
     //   return ['meta' => ['code' => 500, 'message' => 'Fitur ini masih dalam tahap maintenance']];
        (new FM)->required($request,[
    'number'
    ]);

    $client = new Client();

    $response   = $client->request('POST',config('app.url')."/pulsa/voucher.php",[
    'form_params' => [
    'msisdn'  => $request->number
    ]
    ]);

    $responses[]  = json_decode($response->getBody()->getContents(),true);

    if($responses[0]['meta']['code'] == 200)
    {
    $pulsa   = Finance::where('kode','PDT')->first();

    $jsons  = array();
    $datas  = [];

    foreach($responses as $key => $value){
    $jmldata    = count($value['voucher']);
    $jml    = 0;

    for($i = 0; $i < $jmldata; $i++){
    if(preg_match("#\b(DATA|BB|INTERNET)\b#", $value['voucher'][$i]['voucher']))
    {

    $jsons[$jml]['voucher']       = $value['voucher'][$i]['voucher'];
    $jsons[$jml]['product_id']    = $value['voucher'][$i]['product_id'];
    $jsons[$jml]['nominal']       = $value['voucher'][$i]['nominal'];
    $jsons[$jml]['actualprice']   = $value['voucher'][$i]['price'];
    $jsons[$jml]['price']         = $value['voucher'][$i]['price']+$pulsa['keuntungan']+$pulsa['amal'];

    $datas['paketdata']    = $jsons;
    $jml++;
    }
    }
    return (new \Data)->respond([
    'access_token'  => array('token'=>$request->header('Authorization')),
    'data' => $datas
    ]);
    }
    }else{
    $success['code'] = 400;
    $success['message'] = $responses[0]['meta']['message'];
    return response()->json(['meta' => $success]);
    }
    }
}
