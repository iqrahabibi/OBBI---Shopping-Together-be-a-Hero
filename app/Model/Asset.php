<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillable = [
        'nama','nomor','tahun','nilai'
    ];
    
    public function penyusutan(){
        return $this->hasMany('App\Model\Penyusutan');
    }
}
