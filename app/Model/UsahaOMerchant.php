<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\User;
use Auth;

class UsahaOMerchant extends Model
{
    protected $fillable = [
        'usaha_id', 'o_merchant_id', 'kode', 'type', 'modal', 'porsi', 'tanggal_masuk', 'tanggal_keluar', 'valid'
    ];
    
    public function usaha(){
        return $this->belongsTo('App\Model\Usaha')->with('kelurahan');
    }
    
    public function o_merchant(){
        return $this->belongsTo('App\Model\OMerchant');
    }

    public function o_merchant_admin(){
        return $this->hasMany('App\Model\OMerchantAdmin', 'kode', 'kode');
    }

    public function om_barang_grosir(){
        return $this->hasMany('App\Model\OMerchantBarangGrosir','kode','kode_usaha');
    }

    public static function list(){
        $list = array();
        $datas = self::with('usaha','o_merchant')->groupBy('kode')->get();
        foreach($datas as $data){
            $list[$data->kode] = $data->usaha->nama_usaha;
        }
        return $list;
    }

    /**
     * Login Role UsahaOMerchant as OMerchant Admin
     */
    public static function list_for_omerchant_admin(){
        $user_auth = User::find(Auth::user()->id);
        $list = array();
        $datas = self::with('usaha','o_merchant_admin.user')
            ->whereHas('o_merchant_admin', function($o_merchant_admin) use ($user_auth){
                $o_merchant_admin->whereHas('user', function($user) use ($user_auth){
                    $user->where('user_id', $user_auth->id);
                });
                $o_merchant_admin->where('level', 1); // Only Level Kepala can Do a PO
            })
            ->get();
        foreach($datas as $data){
            $list[$data->kode] = $data->usaha->nama_usaha;
        }
        return $list;
    }
}
