<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class OMerchantBarang extends Model
{
    protected $fillable = [
        'kode_omerchant','barang_id','barang_conversi_id','jumlah','periode','publish','harga_satuan'
    ];

    public function omerchant(){
        return $this->belongsTo('App\Model\OMerchant','kode_omerchant','kode');
    }

    public function barang(){
        return $this->belongsTo('App\Model\Barang','barang_id','id');
    }

    public function barang_conversi(){
        return $this->belongsTo('App\Model\BarangConversi');
    }
}
