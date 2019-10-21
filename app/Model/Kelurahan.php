<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Kelurahan extends Model
{
    protected $fillable = [
        'kecamatan_id', 'nama_kelurahan', 'kode_pos'
    ];
    
    public function detail(){
        return $this->hasMany('App\Model\DetailUser')->with('user');
    }

    public function kecamatan(){
        return $this->belongsTo('App\Model\Kecamatan')->with('kota');
    }

    public function target(){
        return $this->hasMany('App\Model\TargetDonasi','kelurahan_id');
    }

    protected $searchable   = [
        'kelurahans.id','kecamatans.nama_kecamatans','nama_kelurahan','kode_pos'
    ];

    protected $orderable    = [
        'kelurahans.id','kecamatans.nama_kecamatans','nama_kelurahan','kode_pos'
    ];

    public function get_searchable()
    {
        return $this->searchable;
    }

    public function get_orderable()
    {
        return $this->orderable;
    }

}
