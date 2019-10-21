<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class GudangKurir extends Model
{
    protected $fillable = [
        'gudang_id', 'nama'
    ];
    
    public function gudang(){
        return $this->belongsTo('App\Model\Gudang');
    }
}
