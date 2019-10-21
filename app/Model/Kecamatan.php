<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Kecamatan extends Model
{
    protected $fillable = [
        'kota_id', 'nama_kecamatan'
    ];
    
    public function kota(){
        return $this->belongsTo('App\Model\Kota')->with('provinsi');
    }

    public function kelurahan(){
        return $this->hasMany('App\Model\Kelurahan');
    }

    protected $searchable   = [
        'kecamatan.id','kotas.nama_kota','nama_kecamatan'
    ];

    protected $orderable    = [
        'kecamatan.id','kotas.nama_kota','nama_kecamatan'
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
