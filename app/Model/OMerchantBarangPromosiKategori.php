<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class OMerchantBarangPromosiKategori extends Model
{
    protected $fillable = [
        'nama_kategori','kode_usaha','deskripsi'
    ];

    public function usaha(){
        return $this->belongsTo('App\Model\UsahaOMerchant','kode_usaha','kode')->with('usaha');
    }

    public static function list(){
        $list = array();
        $datas = self::all();
        foreach($datas as $data){
            $list[$data->id] = $data->nama_kategori . ' - ' . $data->deskripsi;
        }
        return $list;
    }
}
