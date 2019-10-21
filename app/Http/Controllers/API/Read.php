<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helper\Form as FM;

use App\Model\Version;

class Read extends Controller
{
    public function __invoke(Request $request){
        (new FM)->required($request,[
            'code'
        ]);

        $version = Version::where('code',$request->code);

        if($version->count() > 0){
            $success['code']    = 200;
            $version = $version->first();
            if($version->code_baru == $request->code){
                $success['message'] = "Application was up to date";
            }else {
                $success['message'] = "Application wasn't up to date.";
            }

            $version->link     = "https://play.google.com/store/apps/details?id=com.obbiglobalindo.obbitop";

            $data['versi']  = $version;

            return response()->json(['meta' => $success,'data' => $data]);
        }else{
            $success['code']    = 404;
            $success['message'] = "Code not found";

            return response()->json(['meta' => $success]);
        }
    }
}
