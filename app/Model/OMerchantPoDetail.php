<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Helper\Formatting;

class OMerchantPoDetail extends Model
{
    protected $fillable = [
        'o_merchant_po_id','barang_grosir_id','jumlah','harga'
    ];

    public function o_merchant_po(){
        return $this->belongsTo('App\Model\OMerchantPo');
    }

    public function barang_grosir(){
        return $this->belongsTo('App\Model\BarangGrosir','barang_grosir_id','id');
    }
    
    public function rupiah($harga){
        return Formatting::rupiah($harga);
    }

}
