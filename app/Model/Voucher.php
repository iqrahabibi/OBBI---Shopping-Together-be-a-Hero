<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = [
        'jml_voucher', 'sisa'
    ];
    
     public function pelunasan(){
        return $this->hasMany('App\Model\Pelunasan');
    }
}
