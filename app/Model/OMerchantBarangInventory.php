<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class OMerchantBarangInventory extends Model
{
    protected $fillable = ['barang_id','qty','minimal_qty','onhold_qty','harga','urut','kode_usaha'];

    public function barang(){
        return $this->belongsTo('App\Model\Barang');
    }

    public function usaha_om(){
        return $this->belongsTo('App\Model\UsahaOMerchant','kode_usaha','kode');
    }

    protected $searchable   = [
        'o_merchant_barang_inventories.id','barangs.nama_barang','qty','onhold_qty','minimal_qty','harga','urut'
    ];

    protected $orderable    = [
        'o_merchant_barang_inventories.id','barangs.nama_barang','qty','onhold_qty','minimal_qty','harga','urut'
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
