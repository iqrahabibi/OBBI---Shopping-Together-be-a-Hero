<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PengaduanOpf extends Model
{
    protected $fillable = [
        'user_id', 'opf_id','aduan', 'valid'
    ];
    
    public function user(){
        return $this->belongsTo('App\Model\User')->with('detail');
    }
    
    public function opf(){
        return $this->belongsTo('App\Model\Opf');
    }
}