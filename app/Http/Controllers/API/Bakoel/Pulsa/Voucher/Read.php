<?php

namespace App\Http\Controllers\API\Bakoel\Pulsa\Voucher;

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
        //return ['meta' => ['code' => 500, 'message' => 'Fitur ini masih dalam tahap maintenance']];
        (new FM)->required($request, [
    'number'
    ]);

    $client = new Client();

    $response = $client->request('POST', config('app.url') . "/pulsa/voucher.php", [
    'form_params' => [
    'msisdn' => $request->number
    ]
    ]);

    $responses[] = json_decode($response->getBody()
    ->getContents(), true);

    if ( $responses[0]['meta']['code'] == 200 ) {
    $pulsa = Finance::where('kode', 'VCH')
    ->first();

    $json = [];
    $data = [];
    foreach ( $responses as $key => $value ) {
    $jmldata = count($value['voucher']);

    for ( $i = 0; $i < $jmldata; $i++ ) {
    if ( !preg_match("#\b(DATA|BB|INTERNET)\b#", $value['voucher'][$i]['voucher']) ) {
    $pulsa = Finance::where('kode', 'VCH')
    ->first();

    $success['code'] = 200;
    /*$json[$i]['voucher'] = $value['voucher'][$i]['voucher'];
    $json[$i]['product_id'] = $value['voucher'][$i]['product_id'];
    $json[$i]['nominal'] = $value['voucher'][$i]['nominal'];
    $json[$i]['actualprice'] = $value['voucher'][$i]['price'];
    $json[$i]['price'] = $value['voucher'][$i]['price'] + $pulsa['keuntungan'] + $pulsa['amal'];*/
    $json[] = [
    'voucher'     => $value['voucher'][$i]['voucher'],
    'product_id'  => $value['voucher'][$i]['product_id'],
    'nominal'     => $value['voucher'][$i]['nominal'],
    'actualprice' => $value['voucher'][$i]['price'],
    'price'       => $value['voucher'][$i]['price'] + $pulsa['keuntungan'] + $pulsa['amal']
    ];

    $data['voucher'] = $json;
    }
    }

    return (new \Data)->respond([
    'access_token' => [ 'token' => $request->header('Authorization') ],
    'data'         => $data
    ]);

    // return response()->json(['meta' => $success,'data' => $data]);
    }

    } else {
    $success['code'] = 400;
    $success['message'] = $responses[0]['meta']['message'];

    return response()->json([ 'meta' => $success ]);
    }
    }
}
