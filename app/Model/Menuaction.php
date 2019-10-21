<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Menuaction extends Model
{
    protected $fillable = ['menu_id','actionname'];

    public function actionuser()
    {
        return $this->hasOne('App\Model\Useractionmenu');
    }
}
