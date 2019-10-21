<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Divisi extends Model
{
    protected $fillable = [
        'nama_divisi'
    ];

    public function Divisi(){
        return $this->hasMany('App\Model\Divisi');
    }
    public function Karyawan(){
        return $this->hasMany('App\Model\Karyawan');
    }

    public static function list_for_divisi(){
        $list = array();
        $datas = self::all();
        foreach($datas as $data){
            $list[$data->id] = $data->nama_divisi;
        }
        return $list;
    }
// protected $searchable = [
//     'id','nama_divisi'
// ];

// protected $orderable = [
//     'id','nama_divisi'
// ];

// public function get_searchable()
// {
//     return $this->searchable;
// }

// public function get_orderable()
// {
//     return $this->orderable;
// }
}
