<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class BarangGambar extends Model
{
    protected $fillable = ['barang_id','gambar_barang'];
    
    public function barang(){
        return $this->belongsTo('App\Model\Barang','barang_id');
    }
}
