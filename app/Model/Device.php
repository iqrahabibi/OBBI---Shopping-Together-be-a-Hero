<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{

    protected $fillable = ['token_id','nama','user_id','tipe'];

    public function user(){
        return $this->belongsTo('App\Model\User');
    }
}
