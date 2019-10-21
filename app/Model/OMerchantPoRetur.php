<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class OMerchantPoRetur extends Model
{
    protected $fillable = [
        'o_merchant_po_id', 'no_faktur', 'barang_grosir_id', 'jumlah'
    ];

    public function o_merchant_po(){
        return $this->belongsTo('App\Model\OMerchantPo');
    }

    public function barang_grosir(){
        return $this->belongsTo('App\Model\BarangGrosir','barang_grosir_id','id');
    }
}
