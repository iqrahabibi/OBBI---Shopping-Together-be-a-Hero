<?php

namespace App\Model;

use App\Model\OMerchantAdmin;
use Auth;

use Illuminate\Database\Eloquent\Model;

class OMerchantBarangVarian extends Model
{
    protected $fillable = ['barang_id','varian_barang','kode_usaha'];

    public function barang(){
        return $this->belongsTo('App\Model\Barang');
    }

    public function usaha(){
        return $this->belongsTo('App\Model\UsahaOMerchant','kode_usaha','kode')->with('usaha');
    }

    public function om_barang_grosir(){
        return $this->hasMany('App\Model\OMerchantBarangGrosir');
    }

    protected $searchable   = [
        'o_merchant_barang_varians.id','barangs.nama_barang','varian_barang'
    ];

    protected $orderable    = [
        'o_merchant_barang_varians.id','barangs.nama_barang','varian_barang'
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
        $kode_usaha    = OMerchantAdmin::where('user_id',Auth::user()->id)->first();

        $list = array();
        $datas = self::with('barang','usaha')->where('kode_usaha',$kode_usaha->kode)->get();
        foreach($datas as $data){
            $list[$data->id] = $data->varian_barang.' - '.$data->barang->nama_barang;
        }
        return $list;
    }
}
