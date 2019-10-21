<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class BarangStokOpname extends Model
{
    protected $fillable = [
        'tanggal', 'keterangan'
    ];
    
    public function barang_stok_opname_detil(){
        return $this->hasOne('App\Model\BarangStokOpnameDetil');
    }
}
