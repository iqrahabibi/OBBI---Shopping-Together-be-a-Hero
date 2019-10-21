<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ReferalHerobi extends Model {
    protected $fillable = [
        'user_id', 'herobi_id', 'valid'
    ];

    public function user () {
        return $this->belongsTo('App\Model\User')->with('detail');
    }

    public function herobi () {
        return $this->belongsTo('App\Model\Herobi');
    }
}
