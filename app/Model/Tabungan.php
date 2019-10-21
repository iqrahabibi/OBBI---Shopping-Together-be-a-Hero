<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Tabungan extends Model
{
    protected $fillable = [
        'usaha_o_merchant_id', 'type', 'jumlah'
    ];
    
    public function usaha_o_merchant(){
        return $this->belongsTo('App\Model\UsahaOMerchant');
    }
}
