<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class OMPelunasan extends Model
{
    protected $fillable = [
        'om_voucher_id', 'om_po_id','tanggal'
    ];
    
    public function om_voucher(){
        return $this->belongsTo('App\Model\OMVoucher','om_voucher_id');
    }

    public function om_po(){
        return $this->belongsTo('App\Model\OMerchantPo');
    }
}