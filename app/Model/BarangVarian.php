<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class BarangVarian extends Model
{
    protected $fillable = ['barang_id','varian_barang'];

    public function barang(){        
        return $this->belongsTo('App\Model\Barang','barang_id');
    }

    public static function list(){
        $list = array();
        $datas = self::with('barang')->get();
        foreach($datas as $data){
            $list[$data->id] = $data->barang->nama_barang . ' - ' . $data->varian_barang;
        }
        return $list;
    }
}
