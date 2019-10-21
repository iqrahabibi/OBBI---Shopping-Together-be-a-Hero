<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Finance extends Model
{
    protected $fillable = [
        'kode','deskripsi','keuntungan','amal','valid'
    ];
    public function digipay(){
        return $this->hasMany('App\Model\DigiPay');
    }
}
