<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

use Auth;

class OMerchant extends Model
{
    protected $fillable = [
        'user_id', 'referal'
    ];

    public function user(){
        return $this->belongsTo('App\Model\User');
    }

    // public function omerchantadmin(){
    //     return $this->belongsTo('App\Model\OMerchantAdmin');
    // }

    // public function om_po(){
    //     return $this->hsMany('App\Model\OMerchantPo');
    // }

    public static function list_for_tabungan(){
        $list = array();
        $datas = self::with('user' )->get();
        foreach($datas as $data){
            $list[$data->id] = $data->user->fullname . ' - ' . $data->user->email;
        }
        return $list;
    }

    public static function list_for_usaha_omerchant(){
        $list = array();
        $datas = self::with('user')->get();
        foreach($datas as $data){
            $list[$data->id] = $data->user->fullname . ' - ' . $data->user->email;
            // $list[$data->id] = $data->omerchantadmin->alamat . ' - ' . $data->kode . ' - ' . $data->nama_omerchant;
        }
        return $list;
    }

    public function scopeNama($query, $id)
    {
        $data = self::with('user')->find($id);
        return $data->user->fullname . ' - ' . $data->user->email;
    }
}
