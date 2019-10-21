<?php

namespace App\Http\Controllers\API\Referal;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Referal;
use App\User;
use App\Herobi;
use App\Userbalance;

use DB;

class Read extends Controller
{
    public function index(Request $request){
        $referal = new Referal();

        $searchable = $referal->get_searchable();
        $orderable  = $referal->get_orderable();

        $orderby    = 'id';
        $ordertype  = 'asc';
        $show       = 10;

        $referal    = $referal->where([
            ['is_active','=',1],
            ['code_referal','=',$request->code_referal]
        ]);

        if($request->input('show'))
        {
            $show = $request->show;
        }

        foreach(['code_referal','users.fullname','referals.created_at','referals.id'] as $key => $value)
        {
            $searchable[]   = $value;
            $orderable[]    = $value;
        }

        if($request->input('orderby'))
        {
            if(!in_array($request->orderby,$orderable))
            {
                $success['code']    = 401;
                $success['message'] = "can't ordering by $request->orderby";

                return response()->json(['meta'=> $success]);
            }

            $orderby = $request->orderby;
        }

        if($request->input('ordertype'))
        {
            $ordertype  = $request->ordertype;
        }

        if($request->input('search'))
        {
            $herobi   = $herobi->where(function ($query) use ($request, $searchable)
            {
                foreach($searchable as $search)
                {
                    $query->orwhere($search, 'like', "%$request->search%");
                }
            });
        }

        $referal                = $referal->with(['herobi'])->paginate($show);
        $data['access_token']   = array('token'=>$request->header('Authorization'));
        $data['referal']        = $referal;
        
        $success['code']    = 200;
        return response()->json([
            'meta' => $success,
            'data' => $data,
            'option' => [
                'searchable' => $searchable,
                'orderable' => $orderable
            ]
        ]);
    }
}
