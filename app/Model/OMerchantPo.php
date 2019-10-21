<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class OMerchantPo extends Model
{

    protected $fillable = [
        'kode', 'nomor_po', 'gudang_id', 'tanggal', 'match', 'total', 'total_masuk', 'tanggal_po_masuk', 'tanggal_batas_retur', 'status'
    ];

    public function usaha_o_merchant(){
        return $this->belongsTo('App\Model\UsahaOMerchant','kode','kode');
    }

    public function gudang(){
        return $this->belongsTo('App\Model\Gudang');
    }

    public function o_merchant_po_detail(){
        return $this->hasMany('App\Model\OMerchantPoDetail');
    }

    public static function list_for_om_pelunasan(){
        $list = array();
        $datas = self::all();
        foreach($datas as $data){
                $list[$data->id] = $data->nomor_po . ' - ' . $data->total_masuk;
        }
        return $list;
    }

}
