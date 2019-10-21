<?php

namespace App\Http\Controllers\API\Toko\Daerah;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\CartOnlineDetailDaerah;
use App\Model\User;
use App\Model\UsahaOmerchant;
use App\Model\OMerchantAdmin;

use DB;

class Read extends Controller
{
    protected static $om_admin;

    public function __invoke(Request $request){

        self::$om_admin   = OMerchantAdmin::where('user_id',$request->user_id)->first();

        $checkout   = new Checkout();

        $searchable = $checkout->get_searchable();
        $orderable  = $checkout->get_orderable();

        $checkout   = $checkout->where('tipe_belanja','lokal');

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
            $checkout   = $checkout->where(function ($query) use ($request, $searchable)
            {
                foreach($searchable as $search)
                {
                    $query->orwhere($search, 'like', "%$request->search%");
                }
            });
        }

        $checkout   = $checkout->with('alamat','cart','cart.om_barang_inventory',
        'cart.detail_daerah','cart.om_barang_inventory.usaha_om')
        ->whereHas('cart',function($query0){
            $query0->whereHas('om_barang_inventory',function($query1){
                $query1->whereHas('usaha_om',function($query2){
                    $query2->where('kode_usaha',self::$om_admin->kode);
                });
            });
        })
        ->orderby($orderby,$ordertype)->paginate($show);

        return response()->json([
            'access_token' => array('token'=>$request->header('Authorization')),
            'checkout' => $checkout,
            'option' => [
                'searchable' => $searchable,
                'orderable' => $orderable
            ]
        ]);
    }
}
