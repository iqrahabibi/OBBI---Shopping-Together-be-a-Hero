<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class DigiPay extends Model {
    protected $fillable = [
        'user_id', 'finance_id', 'invoice', 'awal', 'jumlah', 'akhir', 'trxid', 'notes', 'phone'
        , 'tipe_token', 'token_number', 'kode', 'valid'
    ];

    public function finance () {
        return $this->belongsTo('App\Mode\Finance');
    }

    public function user () {
        return $this->belongsTo('App\Model\User')->with('detail');
    }

    protected $searchable = [
        'id', 'invoice', 'awal', 'akhir', 'jumlah', 'akhir', 'notes', 'kode', 'users.fullname'
    ];

    protected $orderable = [
        'id', 'invoice', 'awal', 'akhir', 'jumlah', 'akhir', 'notes', 'kode', 'users.fullname'
    ];

    public function get_searchable () {
        return $this->searchable;
    }

    public function get_orderable () {
        return $this->orderable;
    }

    public function detail_bakoel () {
        return $this->hasMany(DetailTransaksiBakoel::class, 'digipay_id', 'id');
    }
}
