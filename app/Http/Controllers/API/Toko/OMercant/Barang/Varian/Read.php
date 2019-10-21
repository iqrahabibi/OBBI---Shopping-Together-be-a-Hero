<?php

namespace App\Http\Controllers\API\Toko\OMercant\Barang\Varian;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\OMerchantAdmin;
use App\Model\OMerchantBarangVarian;

use DB;

class Read extends Controller
{
    public function __invoke(Request $request){
        $om_admin   = OMerchantAdmin::where('user_id',$request->user_id)->first();

        $om_barang_varian   = new OMerchantBarangVarian();

        $searchable = $om_barang_varian->get_searchable();
        $orderable  = $om_barang_varian->get_orderable();

        $om_barang_varian   = $om_barang_varian->where('kode_usaha',$om_admin->kode);

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
                throw new \OptionException("can't ordering by $request->orderby");
            }

            $orderby = $request->orderby;
        }

        if($request->input('ordertype'))
        {
            $ordertype  = $request->ordertype;
        }

        if($request->input('search'))
        {
            $om_barang_varian   = $om_barang_varian->where(function ($query) use ($request, $searchable)
            {
                foreach($searchable as $search)
                {
                    $query->orwhere($search, 'like', "%$request->search%");
                }
            });
        }

        $om_barang_varian   = $om_barang_varian->with('usaha','barang')->orderby($orderby,$ordertype)->paginate($show);

        return response()->json([
            'access_token' => array('token'=>$request->header('Authorization')),
            'om_barang_varian' => $om_barang_varian,
            'option' => [
                'searchable' => $searchable,
                'orderable' => $orderable
            ]
        ]);
    }
}
