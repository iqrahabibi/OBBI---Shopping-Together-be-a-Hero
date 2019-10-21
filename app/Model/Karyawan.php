<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    protected $fillable = [
        'divisi_id', 'jabatan_id', 'nama_karyawan', 'alamat', 'tanggal_lahir', 'tempat_lahir', 'handphone1', 'handphone2', 'tanggal_masuk'
    ];
    public function divisi(){
        return $this->belongsTo('App\Model\Divisi');
    }

    public function jabatan(){
        return $this->belongsTo('App\Model\Jabatan');
    }
   
    public static function list_for_salary(){
        $list = array();
        $datas = self::all();
        foreach($datas as $data){
        $list[$data->id] = $data->nama_karyawan;
       }
       return $list;
        }    

    public static function list_for_absensi(){
       $list = array();
       $datas = self::all();
       foreach($datas as $data){
       $list[$data->id] = $data->nama_karyawan;
      }
       return $list;
       }    
    // protected $searchable   = [
    //     'karyawans.id','divisis.nama_divisi','nama_divisi'
    // ];

    // protected $orderable    = [
    //     'karyawans.id','divisis.nama_divisi','nama_divisi'
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
