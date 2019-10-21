<?php

namespace App\Http\Controllers\API\Saldo;

use App\Http\Controllers\Controller;
use App\Model\DigiPay;
use Illuminate\Http\Request;

class History extends Controller
{
    public function __invoke(Request $request)
    {
        $digi_pay = new DigiPay();

        $searchable = $digi_pay->get_searchable();
        $orderable = $digi_pay->get_orderable();

        $digi_pay = $digi_pay->where([
            ['user_id', '=', $request->user_id],
            ['invoice', '!=', ""],
        ]);

        $orderby = 'id';
        $ordertype = 'desc';
        $show = 10;

        if ($request->input('show')) {
            $show = $request->show;
        }

        if ($request->input('orderby')) {
            if (!in_array($request->orderby, $orderable)) {
                return ["meta" => ["code" => 500, "message" => "can't ordering by $request->orderby"]];
                //throw new \OptionException("can't ordering by $request->orderby");
            }

            $orderby = $request->orderby;
        }

        if ($request->input('ordertype')) {
            $ordertype = $request->ordertype;
        }

        if ($request->input('search')) {
            $digi_pay = $digi_pay->where(function ($query) use ($request, $searchable) {
                foreach ($searchable as $search) {
                    $query->orwhere($search, 'like', "%$request->search%");
                }
            });
        }

        $digi_pay = $digi_pay->with('user')->orderBy($orderby, $ordertype)->paginate($show);

        foreach ($digi_pay as $key => $value) {
            if ($value->valid == 1 && $value->code == 0) {
                $value->status_transaction = 1;
            } else if ($value->valid == 0 && $value->code != 0) {
                $value->status_transaction = 2;
            } else if ($value->valid == 3 && $value->code != 0) {
                $value->status_transaction = 3;
            }
        }

        $success['code'] = 200;

        return response()->json([
            'meta' => $success,
            'access_token' => array('token' => $request->header('Authorization')),
            'saldo' => $digi_pay,
            'option' => [
                'searchable' => $searchable,
                'orderable' => $orderable,
            ],
        ]);
    }
}
