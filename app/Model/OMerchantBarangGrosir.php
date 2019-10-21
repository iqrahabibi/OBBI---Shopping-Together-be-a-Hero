<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class OMerchantBarangGrosir extends Model
{
    protected $fillable = ['barang_id','qty','harga_jual','varian_id','kode_usaha'];

    public function barang(){
        return $this->belongsTo('App\Model\Barang','barang_id')->with('om_gambar');
    }

    public function usaha_om(){
        return $this->belongsTo('App\Model\UsahaOMerchant','kode_usaha','kode');
    }

    public function varian(){
        return $this->belongsTo('App\Model\OMerchantBarangVarian');
    }

    protected $searchable   = [
        'o_merchant_barang_grosirs.id','qty','o_merchant_barang_grosirs.kode_usaha','harga_jual','nama_barang','o_merchant_barang_varians.varian_barang','created_at',
    ];

    protected $orderable    = [
        'o_merchant_barang_grosirs.id','qty','o_merchant_barang_grosirs.kode_usaha','harga_jual','nama_barang','o_merchant_barang_varians.varian_barang','created_at',
    ];

    public function get_searchable()
    {
        return $this->searchable;
    }

    public function get_orderable()
    {
        return $this->orderable;
    }

    public static function list(){
        
        $list = array();
        $datas = self::with('barang','varian')->get();
        foreach($datas as $data){
            $list[$data->id] = $data->barang->sku.' - '.$data->barang->nama_barang . ' - ' . 
                $data->barang->brand.' - '.$data->varian->varian_barang;
        }
        return $list;
    }
}
