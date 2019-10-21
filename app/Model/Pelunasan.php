<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Pelunasan extends Model
{
    protected $fillable = [
        'voucher_id', 'purchasing_order_id','tanggal'
    ];
    
    public function voucher(){
        return $this->belongsTo('App\Model\Voucher');
    }

    public function purchasing_order(){
        return $this->belongsTo('App\Model\PurchasingOrder');
    }
}
