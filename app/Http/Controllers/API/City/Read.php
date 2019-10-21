<?php

namespace App\Http\Controllers\API\City;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Kota;
use App\Model\Provinsi;

class Read extends Controller
{
    public function __invoke(Request $request)
    {
        $city = new Kota();

        $searchable = $city->get_searchable();
        $orderable  = $city->get_orderable();
        
        $city       = $city->where('provinsi_id',$request->provinsi_id);

        $orderby    = 'kotas.id';
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
            $city   = $city->where(function ($query) use ($request, $searchable)
            {
                foreach($searchable as $search)
                {
                    $query->orwhere($search, 'like', "%$request->search%");
                }
            });
        }

        $city                 = $city->with('provinsi')->paginate($show);
        $data['access_token']   = array('token'=>$request->header('Authorization'));
        $data['kota']         = $city;
        
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
