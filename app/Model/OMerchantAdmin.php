<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class OMerchantAdmin extends Model
{
    protected $fillable = [
        'user_id', 'level', 'kode', 'gudang_id', 'alamat'
    ];

    public function user(){
        return $this->belongsTo('App\Model\User');
    }

    public function usaha_o_merchant(){
        return $this->belongsTo('App\Model\UsahaOMerchant', 'kode', 'kode');
    }

    public function gudang(){
        return $this->belongsTo('App\Model\Gudang');
    }
    
}
