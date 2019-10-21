<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserAccessMenu extends Model {
    protected $fillable = [ 'menu_id', 'role_id' ];

    public function menu () {
        return $this->belongsTo('App\Model\Menu');
    }

    public function role () {
        return $this->belongsTo('App\Model\Role');
    }
}
