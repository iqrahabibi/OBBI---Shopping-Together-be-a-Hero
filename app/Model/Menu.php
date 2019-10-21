<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = [
        'menuname','url','parent','level','icon','sort','tag','resource'
    ];

    public function accessmenu()
    {
        return $this->hasOne("App\Model\UserAccessMenu");
    }

    public static function list(){
        $list = array();
        $datas = self::where('parent',0)->get();
        foreach($datas as $data){
            $list[$data->id] = $data->menuname;
        }
        return $list;
    }

    public static function setmenu(){
        $list = array();
        $datas = self::where('url','!=','#')->get();
        foreach($datas as $data){
            $list[$data->id] = $data->menuname;
        }
        return $list;
    }
}
