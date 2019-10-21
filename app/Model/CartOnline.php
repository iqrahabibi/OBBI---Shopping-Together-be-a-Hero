<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CartOnline extends Model
{
    protected $fillable = ['flag','user_id','total_qty','total_belanja','status'];

    public function barang_inventory(){
        return $this->belongsTo('App\Model\BarangInventory','nasional_id','id')->with('barang','barang.varian','barang.barang_nasional');
    }

    public function om_barang_inventory(){
        return $this->belongsTo('App\Model\OMerchantBarangInventory','daerah_id','id')->with('barang','barang.varian_om','barang.om_barang_grosir');
    }

    public function user(){
        return $this->belongsTo('App\Model\User');
    }

    public function detail_daerah(){
        return $this->hasMany('App\Model\CartOnlineDetailDaerah','cart_id');
    }

    public function detail_nasional(){
        return $this->hasMany('App\Model\CartOnlineDetailNasional','cart_id');
    }
}
