<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{

    protected $fillable = ['name','description'];

    public function users(){
        return $this->belongsToMany(User::class);
    }

    public static function list_for_office(){
        $list = array();
        $datas = self::where('id', 2)
            ->orWhere('id', 3)
            ->orWhere('id', 7)
            ->orWhere('id', 8)
            ->orWhere('id', 10)
            ->get();
        foreach($datas as $data){
            $list[$data->id] = $data->name;
        }
        return $list;
    }

    public static function setrole($id){
        $role = self::where('id',$id)->first();

        return $role;
    }
}
