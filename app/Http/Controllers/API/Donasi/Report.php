<?php

namespace App\Http\Controllers\API\Donasi;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\DonasiSummary;
use Carbon\Carbon;
use App\Model\User;
use App\Model\Provinsi;
use App\Model\Kota;
use App\Model\Kecamatan;
use App\Model\Kelurahan;
use App\Model\TargetDonasi;
use App\Model\TipeDonasi;
use App\Model\Donasi;

use DB;
use Auth;

class Report extends Controller
{
    public function report_all(Request $request){
        DB::beginTransaction();

        $user   = User::with('detail')->where('id',$request->user_id)
        ->whereHas('detail', function($query){
            $query->where('valid',1);
        })->first();

        $kelurahan  = Kelurahan::where('id',$user->detail->kelurahan_id)->first();
        $kelurahan_summary  = DonasiSummary::where('kelurahan_id',$kelurahan->id)->sum('total_donasi');

        $kecamatan          = Kecamatan::where('id',$kelurahan->kecamatan_id)->first();
        $kecamatan_summary  = DonasiSummary::whereHas('kelurahan',function($query) use ($kecamatan){
            $query->whereHas('kecamatan', function($query2) use ($kecamatan){
                $query2->where('id',$kecamatan->id);
            });
        })->sum('total_donasi');

        $kota          = Kota::where('id',$kecamatan->kota_id)->first();
        $kota_summary  = DonasiSummary::whereHas('kelurahan',function($query) use ($kota){
            $query->whereHas('kecamatan', function($query2) use ($kota){
                $query2->whereHas('kota', function($query3) use ($kota){
                    $query3->where('id',$kota->id);
                });
            });
        })->sum('total_donasi');

        $provinsi         = Provinsi::where('id',$kota->provinsi_id)->first();
        $provinsi_summary  = DonasiSummary::whereHas('kelurahan',function($query) use ($provinsi){
            $query->whereHas('kecamatan', function($query2) use ($provinsi){
                $query2->whereHas('kota', function($query3) use ($provinsi){
                    $query3->whereHas('provinsi', function($query4) use ($provinsi){
                        $query4->where('id',$provinsi->id);
                    });
                });
            });
        })->sum('total_donasi');

        $country    = DonasiSummary::sum('total_donasi');


        $total  = array();
        $total['kelurahan']['nama_kelurahan']    = $kelurahan->nama_kelurahan;
        $total['kelurahan']['total_donation']    = $kelurahan_summary;
        $total['kecamatan']['nama_kecamatan']    = $kecamatan->nama_kecamatan;
        $total['kecamatan']['total_donation']    = $kecamatan_summary;
        $total['kota']['nama_kota']    = $kota->nama_kota;
        $total['kota']['total_donation']    = $kota_summary;
        $total['provinsi']['nama_provinsi']    = $provinsi->nama_provinsi;
        $total['provinsi']['total_donation']    = $provinsi_summary;
        $total['negara']['nama_negara']    = "Indonesia";
        $total['negara']['total_donation']    = $country;

        $success['code']    = 200;
        $success['message'] = 'Berhasil';

        return response()->json(['meta' => $success,'data' => $total]);
    }

    public function history_saik(Request $request){
        DB::beginTransaction();

        $user   = User::with('detail')->where('id',$request->user_id)
        ->whereHas('detail', function($query){
            $query->where('valid',1);
        })->first();

        $donasi = Donasi::with('target_donasi.tipe_donasi','target_donasi.kelurahan',
        'target_donasi.agama','detail_user')->where('detail_user_id',$user->detail->id)
        ->get();

        $array  = array();

        foreach($donasi as $key => $value){
            $array[$key]['target']  = $value->target_donasi->nama_target_donasi;
            $array[$key]['tipe']    = $value->target_donasi->tipe_donasi->nama_tipe_donasi;
            $array[$key]['kelurahan']= $value->target_donasi->kelurahan->nama_kelurahan;
            $array[$key]['agama']   = $value->target_donasi->agama->nama_agama;
            $array[$key]['jumlah']  = $value->jumlah;
            $array[$key]['waktu']   = date('d M Y',strtotime($value->created_at));
        }

        $success['code']    = 200;
        $success['message'] = 'Berhasil';

        $data['history_donasi'] = $array;

        return response()->json(['meta' => $success, 'data' => $data]);
        
    }
}
