<?php

namespace App\Http\Controllers\API\Bakoel\PLN\Pasca;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helper\Form as FM;

use App\Model\DigiPay;

class History extends Controller {
    public function __invoke (Request $request) {
        (new FM)->required($request, [
            'user_id', 'code'
        ]);

        $result = DigiPay::where([
            [ 'user_id', '=', $request->user_id ],
            [ 'notes', 'like', '%' . $request->code . '%' ]
        ])
                         ->orderBy('created_at', 'desc')
                         ->get();

        if ( empty($result) ) {
            throw new \DataNotFoundExceptions('Data tidak ditemukan.', 'digi_pays');
        } else {

            $data['history'] = $result;

            return (new \Data)->respond([
                'access_token' => [ 'token' => $request->header('Authorization') ],
                'data'         => $data
            ]);
        }
    }
}
