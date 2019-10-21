<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserAlamat extends Model
{
    protected $fillable     = [
        'user_id','kode_kecamatan','alamat','phone','type','kelurahan_id'
    ];

    public function user(){
        return $this->belongsTo('App\Model\User');
    }

    public function kelurahan(){
        return $this->belongsTo('App\Model\Kelurahan','kelurahan_id');
    }

    public function kecamatan(){
        return $this->belongsTo('App\Model\Kecamatan','kode_kecamatan','rajaongkir_id');
    }

    protected $searchable   = [
        'id','users.fullname','users.email','type','alamat'
    ];

    protected $orderable    = [
        'id','users.fullname','users.email','type','alamat'
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
