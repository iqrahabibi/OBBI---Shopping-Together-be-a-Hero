<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Helper\Formatting;

class BarangGrosir extends Model
{
    protected $fillable = ['varian_id','gudang_id','qty','harga_jual','barang_id'];

    public function barang(){
        return $this->belongsTo('App\Model\Barang');
    }

    public function gudang(){
        return $this->belongsTo('App\Model\Gudang');
    }

    public function varian(){
        return $this->belongsTo('App\Model\BarangVarian');
    }

    public static function list(){
        $list = array();
        $datas = self::with('barang.inventory','varian')->get();
        foreach($datas as $data){
            if(empty($data->barang->inventory) || empty($data->varian)){
                continue;
            }

            if($data->barang->inventory->qty > 0){
                $list[$data->id] = $data->barang->nama_barang . 
                ' ( Varian ' . $data->varian->varian_barang . ' ) ' .
                ' ( Pembelian '.$data->qty.' dengan harga '.Formatting::rupiah($data->harga_jual).'/pcs )' . 
                ' ( Tersedia '.$data->barang->inventory->qty.' )';
            }
        }
        return $list;
    }
}
