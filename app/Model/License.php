<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class License extends Model
{
    protected $fillable = [
        'kriteria_license_id', 'kelurahan_id', 'nomor_sertifikat', 'nomor_kartu', 
        'file_perjanjian', 'file_sertifikat'
    ];
    
    public function kriteria_license(){
        return $this->belongsTo('App\Model\KriteriaLicense');
    }
    
    public function kelurahan(){
        return $this->belongsTo('App\Model\Kelurahan');
    }
    
    public function jual_license(){
        return $this->hasOne('App\Model\JualLicense');
    }

    public static function list(){
        $list = array();
        $datas = self::with('jual_license')
            ->whereDoesntHave('jual_license', function ($query) {
                $query->whereNotNull('license_id');
            })->get();
        foreach($datas as $data){
            $list[$data->id] = $data->nomor_sertifikat;
        }
        return $list;
    }
}
