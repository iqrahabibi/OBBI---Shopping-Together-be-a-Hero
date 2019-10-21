<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Donasi extends Model
{
    protected $fillable = [
        'target_donasi_id', 'detail_user_id', 'awal', 'jumlah', 'akhir'
    ];

    public function target_donasi(){
        return $this->belongsTo('App\Model\TargetDonasi');
    }

    public function detail_user(){
        return $this->belongsTo('App\Model\DetailUser');
    }
}
