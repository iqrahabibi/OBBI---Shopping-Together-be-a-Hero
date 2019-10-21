<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ReferalOpf extends Model {
    protected $fillable = [
        'user_id', 'opf_id', 'valid'
    ];

    public function user () {
        return $this->belongsTo('App\Model\User')->with('detail');
    }

    public function opf () {
        return $this->belongsTo('App\Model\Opf');
    }

    public function user_opf () {
        return $this->belongsTo(User::class);
    }
}
