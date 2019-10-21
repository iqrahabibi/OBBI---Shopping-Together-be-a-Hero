<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Provinsi extends Model
{
    protected $fillable = [
        'nama_provinsi'
    ];
    
    public function kota(){
        return $this->hasMany('App\Model\Kota');
    }

    protected $searchable   = [
        'id','nama_provinsi'
    ];

    protected $orderable    = [
        'id','nama_provinsi'
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
