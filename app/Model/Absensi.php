<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    protected $fillable = [
        'karyawan_id', 'tanggal_absen', 'absen', 'absen_masuk', 'absen_keluar'
    ];
    public function karyawan(){
        return $this->belongsTo('App\Model\Karyawan');
    }

   
}
