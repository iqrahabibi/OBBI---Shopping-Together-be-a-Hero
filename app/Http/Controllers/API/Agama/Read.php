<?php

namespace App\Http\Controllers\API\Agama;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\Agama;

use DB;

class Read extends Controller
{
    public function __invoke(Request $request){
        $agama = new Agama();

        $searchable = $agama->get_searchable();
        $orderable  = $agama->get_orderable();

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
            $agama   = $agama->where(function ($query) use ($request, $searchable)
            {
                foreach($searchable as $search)
                {
                    $query->orwhere($search, 'like', "%$request->search%");
                }
            });
        }

        $agama                  = $agama->paginate($show);
        $data['access_token']   = array('token'=>$request->header('Authorization'));
        $data['agama']          = $agama;
        
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
