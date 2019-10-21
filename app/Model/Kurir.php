<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Kurir extends Model
{
    protected $fillable = [
        'user_id','desc','weight','value','origin','destination','product','name','code','etd'
    ];
}
