<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class OMerchantBarangGambar extends Model
{
    protected $fillable = ['barang_id','gambar_barang','kode_usaha'];

    public function barang(){
        return $this->belongsTo('App\Model\Barang');
    }

    public function usaha(){
        return $this->belongsTo('App\Model\UsahaOMerchant','kode_usaha','kode')->with('usaha');
    }

    protected $searchable   = [
        'o_merchant_barang_gambars.id','barangs.nama_barang','gambar_barang'
    ];

    protected $orderable    = [
        'o_merchant_barang_gambars.id','barangs.nama_barang','gambar_barang'
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
