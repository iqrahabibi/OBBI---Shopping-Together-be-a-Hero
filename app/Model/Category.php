<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'group_id', 'nama_kategori', 'valid'
    ];
    
    public function group(){
        return $this->belongsTo('App\Model\Group');
    }

    public static function list(){
        $list = array();
        $datas = self::with('group')->get();
        foreach($datas as $data){
            $list[$data->id] = $data->group->nama_group . ' - ' . $data->nama_kategori;
        }
        return $list;
    }
}
