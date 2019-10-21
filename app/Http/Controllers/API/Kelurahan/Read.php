<?php

namespace App\Http\Controllers\API\Kelurahan;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Kelurahan;
use App\Model\Kecamatan;

class Read extends Controller
{
    public function __invoke(Request $request)
    {
        $kelurahan = new kelurahan();

        $searchable = $kelurahan->get_searchable();
        $orderable  = $kelurahan->get_orderable();

        $kelurahan       = $kelurahan->where('kecamatan_id',$request->kecamatan_id);

        $orderby    = 'kelurahans.id';
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
            $kelurahan   = $kelurahan->where(function ($query) use ($request, $searchable)
            {
                foreach($searchable as $search)
                {
                    $query->orwhere($search, 'like', "%$request->search%");
                }
            });
        }

        $kelurahan                 = $kelurahan->with('kecamatan')->paginate($show);
        $data['access_token']   = array('token'=>$request->header('Authorization'));
        $data['kelurahan']         = $kelurahan;
        
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
