<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CartOnlineDetailNasional extends Model
{
    protected $fillable = ['cart_id','qty','harga','barang_id','varian_id','gudang_id','status','detail_kirim'];

    public function cart(){
        return $this->belongsTo('App\Model\CartOnline','cart_id');
    }

    public function barang(){
        return $this->belongsTo('App\Model\Barang','barang_id');
    }

    public function gudang(){
        return $this->belongsTo('App\Model\Gudang','gudang_id');
    }

    public function varian_nasional(){
        return $this->belongsTo('App\Model\BarangVarian','varian_id');
    }
}
