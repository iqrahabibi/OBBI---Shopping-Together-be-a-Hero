<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PurchasingOrderRetur extends Model
{
    protected $fillable = [
        'purchasing_order_id', 'no_faktur', 'jumlah','barang_id'
    ];

    public function purchasing_order(){
        return $this->belongsTo('App\Model\PurchasingOrder','purchasing_order_id');
    }

    public function barang(){
        return $this->belongsTo('App\Model\Barang');
    }
}
