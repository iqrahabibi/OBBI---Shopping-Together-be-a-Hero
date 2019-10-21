<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Alamat_user extends Model
{
    protected $fillable = [
        'user_id', 'nama_tempat', 'alamat'
    ];
    
    public function user(){
        return $this->belongsTo('App\Model\User');
    }

    public function get_searchable()
    {
        return $this->searchable;
    }

    public function get_orderable()
    {
        return $this->orderable;
    }
}
