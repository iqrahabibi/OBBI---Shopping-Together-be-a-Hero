<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class DonasiSummary extends Model
{
    protected $fillable = [
        'kelurahan_id','total_donasi'
    ];

    public function kelurahan(){
        return $this->belongsTo('App\Model\Kelurahan','kelurahan_id');
    }
}
