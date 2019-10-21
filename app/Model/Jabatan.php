<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    protected $fillable = [
        'nama_jabatan'
    ];

    public function jabatan(){
        return $this->hasMany('App\Model\Jabatan');
    }

    public static function list_for_jabatan(){
        $list = array();
        $datas = self::all();
        foreach($datas as $data){
            $list[$data->id] = $data->nama_jabatan;
        }
        return $list;
    }
    // public function divisi(){
    //     return $this->belongTo('App\Model\Divisi');
    // }

    // protected $searchable = [
    //     'jabatan_id','divisis.nama_divisi','nama_jabatan'
    // ];

    // protected $orderable = [
    //     'jabatan_id','divisis.nama_divis','nama_jabatan'
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
