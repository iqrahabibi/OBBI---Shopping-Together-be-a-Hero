<?php

namespace App\Http\Controllers\API\Saldo;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\DigiPay;

use DB;

class Read_transaksi extends Controller
{
    public function __invoke(Request $request){
        $digi_pay   = new DigiPay();

        $searchable = $digi_pay->getsearchable();
        $orderable  = $digi_pay->get_orderable();

        $digi_pay   = $digi_pay->where([
            ['jumlah','>=',0],
            ['valid','=',0],
        ]);

        $orderby    = 'id';
        $ordertype  = 'asc';
        $show       = 10;

        if($request->input('show'))
        {
            $show = $request->show;
        }

        if($request->input('orderby'))
        {
            if(!in_array($request->orderby,$orderable))
            {
                return ["meta"=>["code"=>500, "message"=>"can't ordering by $request->orderby"]];
                //throw new \OptionException("can't ordering by $request->orderby");
            }

            $orderby = $request->orderby;
        }

        if($request->input('ordertype'))
        {
            $ordertype  = $request->ordertype;
        }

        if($request->input('search'))
        {
            $digi_pay   = $digi_pay->where(function ($query) use ($request, $searchable)
            {
                foreach($searchable as $search)
                {
                    $query->orwhere($search, 'like', "%$request->search%");
                }
            });
        }

        $digi_pay   = $digi_pay->with('user')->orderby($orderby,$ordertype)->paginate($show);

        return response()->json([
            'access_token' => array('token'=>$request->header('Authorization')),
            'digi_pay' => $digi_pay,
            'option' => [
                'searchable' => $searchable,
                'orderable' => $orderable
            ]
        ]);
    }
}
