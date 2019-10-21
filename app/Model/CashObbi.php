<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CashObbi extends Model
{
    protected $fillable = ['tipe','kode','user_id','jumlah','cash','status'];

    public function user(){
        return $this->belongsTo('App\Model\User');
    }
}
