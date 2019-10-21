<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PurchasingOrderMasuk extends Model
{
    protected $fillable = [
        'purchasing_order_id', 'no_faktur', 'barang_id', 'barang_conversi_id', 'jumlah', 'harga'
    ];

    public function purchasing_order(){
        return $this->belongsTo('App\Model\PurchasingOrder');
    }

    public function barang(){
        return $this->belongsTo('App\Model\Barang');
    }

    public function barang_conversi(){
        return $this->belongsTo('App\Model\BarangConversi');
    }
}
