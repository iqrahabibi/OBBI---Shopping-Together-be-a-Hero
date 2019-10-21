<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class OMerchantBarangPromosi extends Model
{
    protected $fillable = [
        'om_barang_kategori_id','kode_usaha','judul','min_total_harga_pesanan','jumlah_diskon','diskon',
        'max_jumlah_diskon','kelipatan','tanggal_aktif','tanggal_berakhir','jam_mulai','jam_akhir','hari'
    ];

    public function om_barang_promosi_kategori(){
        return $this->belongsTo('App\Model\OMerchantBarangPromosiKategori','om_barang_kategori_id');
    }

    public function usaha(){
        return $this->belongsTo('App\Model\UsahaOMerchant','kode_usaha','kode')->with('usaha');
    }
}
