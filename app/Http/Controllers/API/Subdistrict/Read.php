<?php

namespace App\Http\Controllers\API\Subdistrict;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Kota;
use App\Model\Kecamatan;

class Read extends Controller
{
    public function __invoke(Request $request)
    {
        $subdistrict = new Kecamatan();

        $searchable = $subdistrict->get_searchable();
        $orderable  = $subdistrict->get_orderable();

        $subdistrict       = $subdistrict->where('kota_id',$request->kota_id);

        $orderby    = 'kecamatan.id';
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
            $subdistrict   = $subdistrict->where(function ($query) use ($request, $searchable)
            {
                foreach($searchable as $search)
                {
                    $query->orwhere($search, 'like', "%$request->search%");
                }
            });
        }

        $subdistrict                 = $subdistrict->with('kota')->paginate($show);
        $data['access_token']   = array('token'=>$request->header('Authorization'));
        $data['kecamatan']         = $subdistrict;
        
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
