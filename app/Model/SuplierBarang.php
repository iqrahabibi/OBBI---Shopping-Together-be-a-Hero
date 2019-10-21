<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Helper\Formatting;

class SuplierBarang extends Model
{
    protected $fillable = ['barang_id','suplier_id','harga_beli','urut'];

    public function barang(){
        return $this->belongsTo('App\Model\Barang','barang_id');
    }

    public function suplier(){
        return $this->belongsTo('App\Model\Suplier','suplier_id');
    }

    public static function listbarang($id)
    {
        $result = array(); $list = [];

        $datas = self::with('barang', 'suplier')->where('suplier_id',$id)->get();
        if($datas){
            foreach ($datas as $data) {
                array_push($list,[
                    'id'=>''.$data->id,
                    'nama'=>$data->suplier->nama_suplier . ' - ' . $data->barang->nama_barang . ' ( ' . Formatting::rupiah($data->harga_beli) . ' )'
                ]);
            }
            $result['hasil'] = $list;
        }else{
            $result['hasil'] = $list;
        }
        return json_encode($result);
    }
}
