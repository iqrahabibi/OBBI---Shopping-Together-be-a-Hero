<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PurchasingOrder extends Model
{
    protected $fillable = [
        'user_id', 'suplier_id', 'gudang_id', 'nomor_po', 'tanggal_po', 'tanggal_po_masuk', 'tanggal_batas_retur', 'total', 'match'
    ];
    
    public function purchasing_order_detil(){
        return $this->hasMany('App\Model\PurchasingOrderDetil')->with('barang','barang_conversi');
    }
    
    public function user(){
        return $this->belongsTo('App\Model\User');
    }
    
    public function suplier(){
        return $this->belongsTo('App\Model\Suplier');
    }

    public function gudang(){
        return $this->belongsTo('App\Model\Gudang');
    }

    public function pelunasan(){
        return $this->hasMany('App\Model\Pelunasan');
    }

    public static function list_for_pelunasan(){
        $list = array();
        $datas = self::all();
        foreach($datas as $data){
                $list[$data->id] = $data->nomor_po . ' - ' . $data->total_masuk;
        }
        return $list;
    }
}
