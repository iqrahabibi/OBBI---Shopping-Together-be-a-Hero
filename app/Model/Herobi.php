<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Herobi extends Model {
    protected $fillable = [
        'user_id', 'ktp', 'kk', 'selfi', 'nik', 'valid', 'notes'
    ];

    protected $searchable = [
        'users.fullname', 'users.email', 'herobis.id'
    ];

    protected $orderable = [
        'users.fullname', 'users.email', 'herobis.id'
    ];

    public function user () {
        return $this->belongsTo('App\Model\User')->with('detail');
    }

    public function referal () {
        return $this->hasMany('App\Model\ReferalHerobi');
    }

    public function get_searchable () {
        return $this->searchable;
    }

    public function get_orderable () {
        return $this->orderable;
    }

    public function is_herobi () {
        
    }
}
