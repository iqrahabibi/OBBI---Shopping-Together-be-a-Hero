<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Opf extends Model {
    protected $fillable = [
        'user_id', 'foto', 'handphone', 'referal', 'valid'
    ];

    public function user () {
        return $this->belongsTo('App\Model\User');
    }

    public function referal () {
        return $this->hasMany('App\Model\ReferalOpf');
    }

    public function aduan () {
        return $this->hasMany('App\Model\PengaduanOpf');
    }

    public function referal_opf () {
        return $this->hasMany(ReferalOpf::class)
                    ->with('user_opf');
    }
}
