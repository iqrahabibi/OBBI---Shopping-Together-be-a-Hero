<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class RoleUser extends Model {
    public $table = 'role_user';

    protected $fillable = [
        'user_id', 'role_id'
    ];

    public function user () {
        return $this->belongsTo('App\Model\User', 'user_id');
    }

    public function role () {
        return $this->belongsTo(Role::class);
    }
}
