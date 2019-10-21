<?php

namespace App\Http\Controllers\API\Alamat;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\UserAlamat;
use App\Model\User;

use DB;

class Read extends Controller
{
    public function __invoke(Request $request)
    {
        $alamat    = new UserAlamat();

        $searchable = $alamat->get_searchable();
        $orderable  = $alamat->get_orderable();

        $alamat     = $alamat->where('user_id',$request->user_id);

        $orderby    = 'alamat_users.id';
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
                throw new \SecurityExceptions("can't ordering by $request->orderby");
            }

            $orderby = $request->orderby;
        }

        if($request->input('ordertype'))
        {
            $ordertype  = $request->ordertype;
        }

        if($request->input('search'))
        {
            $alamat   = $alamat->where(function ($query) use ($request, $searchable)
            {
                foreach($searchable as $search)
                {
                    $query->orwhere($search, 'like', "%$request->search%");
                }
            });
        }

        $alamat                 = $alamat->with('kelurahan.kecamatan')->paginate($show);
        $data['alamat']         = $alamat;
        
        $success['code']    = 200;
        return response()->json([
            'meta'  => $success,
            'data' => $data,
            'option' => [
                'searchable' => $searchable,
                'orderable' => $orderable
            ]
        ]);
    }
}