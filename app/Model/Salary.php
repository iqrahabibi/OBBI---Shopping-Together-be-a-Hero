<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    protected $fillable = [
        'karyawan_id', 'nominal'
    ];
    public function karyawan(){
        return $this->belongsTo('App\Model\Karyawan');
    }
    
    
    }

    // protected $searchable   = [
    //     'karyawans.id','karyawans.nama_karyawan','nominal'
    // ];

    // protected $orderable    = [
    //     'karyawans.id','karyawans.nama_karyawan','nominal'
    // ];

    // public function get_searchable()
    // {
    //     return $this->searchable;
    // }

    // public function get_orderable()
    // {
    //     return $this->orderable;
    // }


