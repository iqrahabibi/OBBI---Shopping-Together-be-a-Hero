<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Gudang extends Model
{
    protected $fillable = [
        'nama_gudang', 'alamat', 'user_id','kelurahan_id'
    ];
    
    public function user(){
        return $this->belongsTo('App\Model\User');
    }

    public function kelurahan(){
        return $this->belongsTo('App\Model\Kelurahan','kelurahan_id');
    }
}
