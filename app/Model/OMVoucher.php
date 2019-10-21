<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class OMVoucher extends Model
{
    protected $fillable = [
        'jml_om_voucher', 'sisa'
    ];
    
     public function ompelunasan(){
        return $this->hasMany('App\Model\OMPelunasan');
    }

    public static function list_voucher()
    {
        $list = array();
        $datas = self::all();
        foreach($datas as $data){
            if($data->sisa != 0 || $data->sisa != null){
                $list[$data->id] = $data->sisa;
            }
                
        }
        return $list;
    }
}