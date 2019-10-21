<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Checkout extends Model
{
    protected $fillable = [
        'cart_id','invoice','alamat_id','total_belanja','status',
        'tipe_pembayaran','tipe_belanja','harga_kirim','expired_at','kode','amal','keuntungan'
    ];

    public function cart(){
        return $this->belongsTo('App\Model\CartOnline','cart_id');
    }

    public function alamat(){
        return $this->belongsTo('App\Model\UserAlamat','alamat_id');
    }

    public function kelurahan(){
        return $this->belongsTo('App\Model\Kelurahan','kelurahan_id');
    }

    protected $searchable = [
        'checkouts.id','total_belanja','status','tipe_pembayaran','user_alamats.alamat'
    ];
    
    protected $orderable = [
        'checkouts.id','total_belanja','status','tipe_pembayaran','user_alamats.alamat'
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
