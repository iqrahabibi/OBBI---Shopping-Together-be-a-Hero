<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class LicenseOwner extends Model
{
    protected $fillable = [
        'nik', 'nama_depan', 'nama_tengah', 'nama_belakang', 'nama_lengkap', 
        'tanggal_lahir', 'agama_id', 'status_pernikahan', 'jenis_kelamin', 'alamat',
        'rt', 'rw', 'no_telp', 'no_telp_2', 'pewaris_id', 'file_ktp', 'valid'
    ];
    
    public function agama(){
        return $this->belongsTo('App\Model\Agama');
    }

    public function child(){
        return $this->hasOne('App\Model\LicenseOwner', 'pewaris_id');
    }
}
