<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TargetDonasi extends Model
{
    protected $fillable = [
        'tipe_donasi_id', 'agama_id', 'nama_target_donasi', 'kelurahan_id'
    ];

    public function tipe_donasi(){
        return $this->belongsTo('App\Model\TipeDonasi');
    }

    public function agama(){
        return $this->belongsTo('App\Model\Agama');
    }

    public function kelurahan(){
        return $this->belongsTo('App\Model\Kelurahan');
    }

    public function donasi(){
        return $this->hasMany('App\Model\Donasi','target_donasi_id');
    }

    public static function list(){
        $list = array();
        $datas = self::with('tipe_donasi', 'agama')->get();
        foreach($datas as $data){
            $list[$data->id] = $data->tipe_donasi->nama_tipe_donasi . ' - ' . 
                $data->agama->nama_agama . ' - ' . 
                $data->nama_target_donasi;
        }
        return $list;
    }
}
