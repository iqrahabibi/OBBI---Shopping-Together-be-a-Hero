<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $fillable = [
        'nama_barang', 'category_id','sku','brand','weight','deskripsi', 'jumlah_amal', 'keuntungan'
    ];
    
    public function category(){
        return $this->belongsTo('App\Model\Category')->with('group');
    }

    public function gambar(){
        return $this->hasMany('App\Model\BarangGambar');
    }

    public function varian(){
        return $this->hasMany('App\Model\BarangVarian');
    }

    public function varian_om(){
        return $this->hasMany('App\Model\OMerchantBarangVarian');
    }

    public function inventory(){
        return $this->hasOne('App\Model\BarangInventory');
    }

    public function om_barang_grosir(){
        return $this->hasMany('App\Model\OMerchantBarangGrosir','barang_id');
    }

    public function om_gambar(){
        return $this->hasMany('App\Model\OMerchantBarangGambar');
    }

    public function barang_nasional(){
        return $this->hasMany('App\Model\BarangNasional');
    }

    public static function list(){
        $list = array();
        $datas = self::with('category.group')->get();
        foreach($datas as $data){
            $list[$data->id] = $data->category->group->nama_group . ' - ' . 
                $data->category->nama_kategori . ' - ' . $data->nama_barang;
        }
        return $list;
    }

    public function scopeNama($query, $value)
    {
        $data = self::with('category.group')->where('nama_barang', $value)->first();
        return $data->category->group->nama_group . ' - ' . $data->category->nama_kategori . ' - ' . $data->nama_barang;
    }
    
    public function om_po_detail(){
        return $this->hasMany('App\Model\OMerchantPoDetail','barang_id');
    }
}
