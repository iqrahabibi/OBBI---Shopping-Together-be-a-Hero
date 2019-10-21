<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Penyusutan extends Model
{
    protected $fillable = [
        'asset_id', 'nilai_awal', 'nilai_akhir','tahun_penyusutan'
    ];
    
    public function asset(){
        return $this->belongsTo('App\Model\Asset');
    }

}