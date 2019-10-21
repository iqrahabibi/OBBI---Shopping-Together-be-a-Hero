<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ReferalOMerchant extends Model
{
    protected $fillable = [
        'user_id', 'o_merchant_id', 'valid'
    ];
    
    public function user(){
        return $this->belongsTo('App\Model\User')->with('detail');
    }
    
    public function o_merchant(){
        return $this->belongsTo('App\Model\OMerchant');
    }
}
