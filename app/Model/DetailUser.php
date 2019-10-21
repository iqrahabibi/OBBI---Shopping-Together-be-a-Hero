<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Helper\Formatting;

class DetailUser extends Model {
    protected $fillable = [
        'user_id', 'kelurahan_id', 'agama_id', 'alamat', 'phone', 'valid', 'firebase'
    ];

    public function agama () {
        return $this->belongsTo('App\Model\Agama');
    }

    public function user () {
        return $this->belongsTo('App\Model\User')
                    ->with('saldo');
    }

    public function kelurahan () {
        return $this->belongsTo('App\Model\Kelurahan')
                    ->with('kecamatan');
    }

    public static function list () {
        $list = [];
        $datas = self::with('user.saldo')
                     ->where('valid', 1)
                     ->whereHas('user', function ($user) {
                         $user->whereHas('saldo', function ($saldo) {
                             $saldo->where('amal', '>', 0);
                         });
                     })
                     ->get();
        foreach ( $datas as $data ) {
            if ( !empty($data->user->saldo) ) {
                $list[$data->id] = $data->user->fullname .
                    ' ( ' . Formatting::rupiah($data->user->saldo->amal) . ' ) di ' .
                    $data->alamat;
            }
        }

        return $list;
    }

    public static function listfordonasi () {
        $list = [];
        $datas = self::with('user.saldo')
                     ->where('valid', 1)
                     ->get();
        foreach ( $datas as $data ) {
            if ( $data->user->saldo->amal != 0 ) {
                $list[$data->id] = $data->user->fullname . ' - ' . $data->user->email;
            }
        }

        return $list;
    }
}
