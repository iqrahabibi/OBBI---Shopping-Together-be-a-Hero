<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Version extends Model
{
    protected $fillable = ['code','name','code_baru','wajib','version'];
}
