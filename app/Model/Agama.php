<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Agama extends Model
{
    protected $fillable = [
        'nama_agama'
    ];

    public function detail(){
        return $this->hasMany('App\Model\DetailUser');
    }

    protected $searchable   = [
        'id','nama_agama'
    ];

    protected $orderable    = [
        'id','nama_agama'
    ];

    public function get_searchable()
    {
        return $this->searchable;
    }

    public function get_orderable()
    {
        return $this->orderable;
    }
}
