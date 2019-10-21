<?php

namespace App\Http\Controllers\API\Saldo;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\Saldo;

use DB;

class Read_saldo extends Controller
{
    public function __invoke(Request $request){
        $saldo  = new Saldo();

        $searchable = $saldo->getsearchable();
        $orderable  = $saldo->get_orderable();

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
            $saldo   = $saldo->where(function ($query) use ($request, $searchable)
            {
                foreach($searchable as $search)
                {
                    $query->orwhere($search, 'like', "%$request->search%");
                }
            });
        }

        $saldo                  = $saldo->with('user')->orderby($orderby,$ordertype)->paginate($show);

        return response()->json([
            'access_token' => array('token'=>$request->header('Authorization')),
            'saldo' => $saldo,
            'option' => [
                'searchable' => $searchable,
                'orderable' => $orderable
            ]
        ]);
    }
}
