<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class BarangStokOpnameDetil extends Model
{
    protected $fillable = [
        'barang_stok_opname_id', 'barang_stok_id', 'jumlah', 'type'
    ];
    
    public function barang_stok(){
        return $this->belongsTo('App\Model\BarangStok');
    }
    
    public function barang_stok_opname(){
        return $this->belongsTo('App\Model\BarangStokOpname');
    }
}
