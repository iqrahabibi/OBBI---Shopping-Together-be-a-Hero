<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class BarangInventory extends Model
{
    protected $fillable = ['qty','onhold_qty','minimal_qty','barang_id','gudang_id'];

    public function barang(){
        return $this->belongsTo('App\Model\Barang','barang_id');
    }

    public function gudang(){
        return $this->belongsTo('App\Model\Gudang','gudang_id');
    }

    protected $searchable   = [
        'barang_inventories.id','barangs.nama_barang','qty','onhold_qty','minimal_qty'
    ];

    protected $orderable    = [
        'barang_inventories.id','barangs.nama_barang','qty','onhold_qty','minimal_qty'
    ];

    public function get_searchable()
    {
        return $this->searchable;
    }

    public function get_orderable()
    {
        return $this->orderable;
    }
}
