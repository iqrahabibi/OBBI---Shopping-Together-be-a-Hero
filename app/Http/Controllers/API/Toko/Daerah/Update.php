<?php

namespace App\Http\Controllers\API\Toko\Daerah;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helper\Form as FM;

use App\Model\Checkout;

use DB;

class Update extends Controller
{
    public function __invoke(Request $request){
        (new FM)->required($request,[
            'checkout_id','status'
        ]);

        DB::beginTransaction();

        $update = Checkout::find($request->checkout_id);

        $update->status = $request->status;

            
    }
}
