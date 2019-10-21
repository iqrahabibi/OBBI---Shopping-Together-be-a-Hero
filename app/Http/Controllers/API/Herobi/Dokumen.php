<?php

namespace App\Http\Controllers\API\Herobi;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

use App\Model\Herobi;
use App\Model\ReferalHerobi;
use App\Model\User;

use Validator;
use Auth;
use DB;
use Mail;

class Dokumen extends Controller
{
    public function __invoke(Request $request){

        $user = User::findOrFail($request->get('user_id'));
        $url_herobi    = "/herobi";
        $data   = $request->all();

        $rules      =   [ 
            'image1' => 'required|image|max:1028',
            'image2' => 'required|image|max:1028',
            'image3' => 'required|image|max:1028',
        ];
        
        $messages   =   [
            'required'  => 'Field :attribute harus diisi.',
            'image'     => 'Field :attribute harus berupa gambar (jpeg, png, bmp, gif, or svg).',
            'max'  => 'Field :attribute harus berukuran kurang dari 1 MB.',
        ];

        $validator  = Validator::make($data, $rules, $messages);

        if($validator->fails())
        {
            $errors = $validator->errors();

            $success['code']    = 400;
            $success['message'] = $errors->first();

            return response()->json(['meta'=> $success]);
        }

        DB::beginTransaction();

        try
        {
            $image1 = $request->file('image1');
            $image2 = $request->file('image2');
            $image3 = $request->file('image3');

            $extension1 = Str::lower($image1->getClientOriginalExtension());
            $extension2 = Str::lower($image2->getClientOriginalExtension());
            $extension3 = Str::lower($image3->getClientOriginalExtension());

            $filename1  = str_random(40). '.' . $extension1;
            $filename2  = str_random(40). '.' . $extension2;
            $filename3  = str_random(40). '.' . $extension3;

            $destinationPath    = storage_path().DIRECTORY_SEPARATOR.'app/public'.$url_herobi;

            $image1->move($destinationPath,$filename1);
            $image2->move($destinationPath,$filename2);
            $image3->move($destinationPath,$filename3);

            $result = Herobi::where('user_id',$user->id)->first();
            
            if(!empty($result) && $result->valid == 0)
            {
                $success['code']    = 400;
                $success['message'] = "Masih ada dokumen yang belum di verifikasi.";

                return response()->json(['meta'=> $success]);
            }else if(!empty($result) && $result->valid == 1)
            {
                $success['code']    = 400;
                $success['message'] = "Dokumen sudah terverifikasi.";

                return response()->json(['meta'=> $success]);
            }else{
                $herobi = Herobi::updateOrCreate([
                    'user_id'   => $user->id,
                    'valid'     => 2,
                ], [
                    'ktp'    => $url_herobi."/".$filename1,
                    'kk'    => $url_herobi."/".$filename2,
                    'selfi'    => $url_herobi."/".$filename3,
                    'valid'     => 0,
                ]);

                $referal = $request->get('referal');

                if(!empty($referal))
                {
                    $user_referee   = User::where('referal', $referal)->first();

                    if(empty($user_referee))
                    {
                        $success['code']    = 404;
                        $success['message'] = "Kode referal tidak ditemukan.";

                        return response()->json(['meta'=> $success]);
                    }else{
                        $referal    = ReferalHerobi::where('herobi_id',$herobi->id)
                                        ->where('user_id',$user_referee->id)
                                        ->first();

                        if(!empty($referal) && $referal->valid != 2)
                        {
                            $success['code']    = 404;
                            $success['message'] = "Anda sudah melakukan referal.";

                            return response()->json(['meta'=> $success]);
                        }else{
                            $referalherobi = ReferalHerobi::updateOrCreate([
                                'herobi_id'    => $herobi->id,
                                'user_id'    => $user_referee->id
                            ],[
                                'valid'     => 0
                            ]);
                        }
                    }
                }

                DB::commit();
                
                Mail::send('auth.email.herobiuser',compact('referal','user'),function($m) use ($user){
                    $m->to($user->email,$user->fullname)->subject('[OBBI Application] Notification Pengajuan HEROBI');
                });

                Mail::send('auth.email.herobi',compact('user','herobi'),function($m) use ($result){
                    $m->to('jokopriyono0201@gmail.com', 'Joko')->subject('Notification Pengajuan HEROBI');
                    
                });

                $success['code']    = 200;
                $success['message'] = "sukses";

                // $input['access_token']  = array('token' => $request->header('Authorization'));
                
                // $input['herobi']   = $result;
                
                return response()->json(['meta'=> $success]);
            }
        }catch(QueryException $e)
        {
            DB::rollBack();
            $success['code']    = 400;
            $success['message'] = $e->getMessage();

            return response()->json(['meta'=> $success]);
        }
    }
}
