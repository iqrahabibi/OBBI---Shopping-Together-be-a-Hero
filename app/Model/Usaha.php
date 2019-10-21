<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Helper\Formatting;

class Usaha extends Model
{
    protected $fillable = ['kelurahan_id','nama_usaha','deskripsi_usaha','alamat','total_modal','status','latitude','longitude'];

    public function kelurahan(){
        return $this->belongsTo('App\Model\Kelurahan')->with('kecamatan');
    }

    public static function list(){
        $list = array();
        $datas = self::where('status','Open')->get();
        foreach($datas as $data){
            $list[$data->id] = $data->nama_usaha . ' ( ' . Formatting::rupiah($data->total_modal) . ' )';
        }
        return $list;
    }

    public function scopeNama($query, $id)
    {
        $data = $query->find($id);
        return $data->nama_usaha . ' ( ' . Formatting::rupiah($data->total_modal) . ' )';
    }
}
