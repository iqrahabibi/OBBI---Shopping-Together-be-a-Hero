<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Saldo extends Model
{
    protected $fillable = ['saldo','keuntungan','amal','user_id'];

    public function user(){
        return $this->belongsTo('App\Model\User')->with('detail');
    }

    protected $searchable   = [
        'id','saldo','amal','keuntungan','users.fullname'
    ];

    protected $orderable    = [
        'id','saldo','amal','keuntungan','users.fullname'
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
