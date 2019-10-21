<?php

namespace App\Http\Controllers\API\Revoke;

use Illuminate\Auth\AuthenticationException;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\RevokeModel;
use DB;

class Read extends Controller
{
    public function index(Request $request)
    {   
        $showtoken = new RevokeModel();
        $searchable = $showtoken->get_searchable();
        $orderable  = $showtoken->get_orderable();

        $orderby    = 'oauth_access_tokens.id';
        $ordertype  = 'asc';
        $show       = 10;

        if($request->input('show'))
        {
            $show = $request->show;
        }
        foreach(['oauth_access_tokens.id','name'] as $key => $value)
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
            $showtoken   = $showtoken->where(function ($query) use ($request, $searchable)
            {
                foreach($searchable as $search)
                {
                    $query->orwhere($search, 'like', "%$request->search%");
                }
            });
        }
        $showtoken                 = $showtoken->with('user')->paginate($show);
        $data['access_token']   = array('token'=>$request->header('Authorization'));
        $data['showtoken']         = $showtoken;
        
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