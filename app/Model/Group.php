<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = [
        'nama_group'
    ];
    
    public function category(){
        return $this->hasMany('App\Model\Category');
    }
}
