<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Kota extends Model
{
    protected $fillable = [
        'provinsi_id', 'tipe', 'nama_kota'
    ];
    
    public function provinsi(){
        return $this->belongsTo('App\Model\Provinsi');
    }

    public function kecamatan(){
        return $this->hasMany('App\Model\Kecamatan');
    }

    protected $searchable   = [
        'kotas.id','nama_kota','provinsis.nama_provinsi'
    ];

    protected $orderable    = [
        'kotas.id','nama_kota','provinsis.nama_provinsi'
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
