<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CheckoutOffline extends Model
{
    protected $fillable = ['cart_id','invoice','total_belanja','status'];

    public function cart(){
        return $this->belongsTo('App\Model\CartOnline','cart_id');
    }
}
