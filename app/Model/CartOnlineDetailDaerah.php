<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CartOnlineDetailDaerah extends Model
{
    protected $fillable = [
        'cart_id','qty','harga','barang_id','kode','varian_id','status','detail_kirim'
    ];

    public function cart(){
        return $this->belongsTo('App\Model\CartOnline','cart_id');
    }

    public function barang(){
        return $this->belongsTo('App\Model\Barang','barang_id');
    }

    public function usaha_om(){
        return $this->belongsTo('App\Model\UsahaOMerchant','kode','kode');
    }

    public function varian_daerah(){
        return $this->belongsTo('App\Model\OMerchantBarangVarian','varian_id');
    }

    protected $searchable = [
        'cart_online_detail_daerahs.id','carts.total_belanja','cart_online_detail_daerahs.status','varian','qty','harga'
    ];
    
    protected $orderable = [
        'cart_online_detail_daerahs.id','carts.total_belanja','cart_online_detail_daerahs.status','varian','qty','harga'
    ];
    
    public function get_searchable()
    {
        return $this->searchable;
    }
    
    public function get_orderable()
    {
        return $this->orderable;
    }
}
