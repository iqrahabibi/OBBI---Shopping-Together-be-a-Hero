<?php

namespace App\Http\Controllers\API\Province;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Provinsi;

class Read extends Controller
{
    public function __invoke(Request $request)
    {
        $province = new Provinsi();

        $searchable = $province->get_searchable();
        $orderable  = $province->get_orderable();

        $province   = $province->where('nama_provinsi','LIKE','%'.$request->nama_provinsi.'%');

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
            $province   = $province->where(function ($query) use ($request, $searchable)
            {
                foreach($searchable as $search)
                {
                    $query->orwhere($search, 'like', "%$request->search%");
                }
            });
        }

        $province                 = $province->paginate($show);
        $data['access_token']   = array('token'=>$request->header('Authorization'));
        $data['provinsi']         = $province;
        
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
