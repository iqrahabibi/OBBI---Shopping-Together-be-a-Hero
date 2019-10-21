<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class BarangConversi extends Model
{
    protected $fillable = [
        'parent_id', 'satuan', 'jumlah'
    ];
    
    public function child(){
        return $this->hasOne('App\Model\BarangConversi', 'parent_id');
    }
    
    public function parent(){
        return $this->belongsTo('App\Model\BarangConversi', 'parent_id');
    }

    public static function list(){
        $list = array();
        $datas = self::all();
        foreach($datas as $data){
            $list[$data->id] = $data->satuan;
        }
        return $list;
    }
}
