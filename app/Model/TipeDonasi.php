<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TipeDonasi extends Model
{
    protected $fillable = [
        'nama_tipe_donasi'
    ];

    public function targetdonasi(){
        return $this->hasMany('App\Model\TargetDonasi');
    }
}
