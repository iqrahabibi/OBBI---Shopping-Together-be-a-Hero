<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class JualLicense extends Model
{
    protected $fillable = [
        'license_owner_id', 'license_id', 'tanggal_jual', 'perolehan', 'jenis_pembayaran'
    ];
    
    public function license_owner(){
        return $this->belongsTo('App\Model\LicenseOwner');
    }
    
    public function license(){
        return $this->belongsTo('App\Model\License');
    }
}
