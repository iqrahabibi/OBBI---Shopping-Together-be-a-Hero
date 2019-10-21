<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class BarangStok extends Model
{
    protected $fillable = [
        'parent_id', 'barang_id', 'barang_conversi_id', 'gudang_id', 'jumlah', 'periode', 'publish', 'harga_satuan'
    ];
    
    public function child(){
        return $this->hasOne('App\Model\BarangStok', 'parent_id');
    }
    
    public function parent(){
        return $this->belongsTo('App\Model\BarangStok', 'parent_id');
    }
    
    public function barang(){
        return $this->belongsTo('App\Model\Barang');
    }
    
    public function barang_conversi(){
        return $this->belongsTo('App\Model\BarangConversi');
    }
    
    public function gudang(){
        return $this->belongsTo('App\Model\Gudang');
    }

    public static function list(){
        $list = array();
        $datas = self::with('barang', 'barang_conversi')->get();
        foreach($datas as $data){
            $list[$data->id] = $data->barang->nama($data->barang->nama_barang) . ' - ' . 
                $data->jumlah . ' ' . $data->barang_conversi->satuan . ' - ' . $data->periode;
        }
        return $list;
    }

    public static function listopname(){
        $list = array();
        $datas = self::with('barang', 'barang_conversi')->where('parent_id', null)->get();
        foreach($datas as $data){
            $list[$data->id] = $data->barang->nama($data->barang->nama_barang) . ' - ' . 
                $data->jumlah . ' ' . $data->barang_conversi->satuan . ' - ' . $data->periode;
        }
        return $list;
    }
}
