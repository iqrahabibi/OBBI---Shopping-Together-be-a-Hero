<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Suplier extends Model
{
    protected $fillable = [
        'nama_suplier'
    ];

    public function detail(){
        return $this->hasMany('App\Model\Suplier');
    }
}
