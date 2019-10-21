<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class BarangNasional extends Model
{
    protected $fillable = [
        'barang_id','varian_id','gudang_id','qty','harga_beli','harga_jual','urut'
    ];

    public function barang(){
        return $this->belongsTo('App\Model\Barang')->with('gambar');
    }

    public function varian(){
        return $this->belongsTo('App\Model\BarangVarian');
    }

    public function gudang(){
        return $this->belongsTo('App\Model\Gudang');
    }

    protected $searchable   = [
        'barang_nasionals.id','barangs.nama_barang','gudangs.nama_gudang','barang_varians.varian_barang','qty','harga_beli','harga_jual'
    ];

    protected $orderable    = [
        'barang_nasionals.id','barangs.nama_barang','gudangs.nama_gudang','barang_varians.varian_barang','qty','harga_beli','harga_jual'
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
