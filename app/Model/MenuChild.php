<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class MenuChild extends Model
{
    protected $fillable = [
        'menu_id', 'kode', 'nama'
    ];
}
