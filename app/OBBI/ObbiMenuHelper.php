<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Model\Menu;
use App\Model\UserAccessMenu;
use App\Model\RoleUser;
use App\Model\Role;
use App\Model\User;
use Illuminate\Support\Facades\Route;

class ObbiMenuHelper{

    protected static $path = '';

    public static function generate_menu(){

        $currentRoute   = Route::currentRouteName();

        self::$path   = explode('.', $currentRoute)[0];

        $roles = RoleUser::with('role')->where('user_id',Auth::user()->id)->get();

        $nav        = "";
        $activeMenu = ObbiMenuHelper::generateActive(self::$path);

        foreach($roles as $key => $value){
            $nav .= "<li><a><span class='title'>".$value->role->name."</span></a>";

            $menu       = DB::select("select a.id, a.resource, menuname, url, parent, level, icon from menus a join user_access_menus b on a.id = b.menu_id where role_id = '$value->role_id' order by a.id");

            foreach($menu as $key2 => $value2){
                if($value2->level == 0){
                    $menu0[$value2->id]["menuid"]  = $value2->id;
                    $menu0[$value2->id]["name"]    = $value2->menuname;
                    $menu0[$value2->id]["link"]    = $value2->url;
                    $menu0[$value2->id]["parent"]  = $value2->parent;
                    $menu0[$value2->id]["urutan"]  = $value2->level;
                    $menu0[$value2->id]["icon"]    = $value2->icon;
                    $menu0[$value2->id]['resource']= $value2->resource;
                }
                
                elseif($value2->level == 1){
                    $menu1[$value2->id]["menuid"]  = $value2->id;
                    $menu1[$value2->id]["name"]    = $value2->menuname;
                    $menu1[$value2->id]["link"]    = $value2->url;
                    $menu1[$value2->id]["parent"]  = $value2->parent;
                    $menu1[$value2->id]["urutan"]  = $value2->level;
                    $menu1[$value2->id]["icon"]    = $value2->icon;
                    $menu1[$value2->id]['resource']= $value2->resource;
                }

            }

            if(!empty($menu1)){
                uasort($menu1, function($a, $b) {
    
                $a1 = $a["name"];
                $b1 = $b["name"];
    
                $out = strcasecmp($a1,$b1);
                    if($out == 0){ return  0;}
                    if($out >  0){ return  1;}
                    if($out <  0){ return -1;}
                });
            }

            if(!empty($menu0)){
                uasort($menu0, function($a, $b) {
    
                $a1 = $a["name"];
                $b1 = $b["name"];
    
                $out = strcasecmp($a1,$b1);
                    if($out == 0){ return  0;}
                    if($out >  0){ return  1;}
                    if($out <  0){ return -1;}
                });
            }

            $counter1   = 0;
            $max1       = 0;

            if(!empty($menu0)){
                foreach ($menu0 as $key0 => $value0) {
                
                    $nav .= "<li class='". ObbiMenuHelper::activeCheck($value0) ."'><a href='" . $value0["link"] . "'><i class='" . $value0["icon"] . "'></i> <span class='title'>" . $value0["name"] . "</span>";

                    if(!empty($menu1)){
                        $nav .= ObbiMenuHelper::hasChild($menu1, $key0);

                        foreach ($menu1 as $key1 => $value1){
                            if ($value1["parent"]==$key0) {

                                if ($counter1==0) {
                                    $counter1++;
                                    foreach ($menu1 as $key99 => $value99) if ($value99["parent"]==$key0) $max1++;
                                    $nav .= "<ul class='sub-menu'>";
                                }else{$counter1++;}
                                    
                                    $nav .= "<li class='" . ObbiMenuHelper::activeCheckChild($key1, $activeMenu) . "'><a href='". $value1["link"] ."'> <i class='".$value1['icon']."'></i> " . $value1["name"] . "</a>";

                                if ($counter1==$max1) $nav .= "</ul>";

                            }else{continue;}
                        }
                    }else{
                        $nav .= "</a>";
                    }

                    $counter1   = 0;
                    $max1       = 0;

                    $nav .= "</li>";
                }
            }

            $menu0 = null;
            $menu1 = null;
        }

        return $nav;

    }

    private static function hasChild($menu, $key){
        $arrowed1 = false;

        foreach($menu as $arrow1 => $arrows1){
            if($arrows1["parent"]==$key){
                $arrowed1 = true;
                return "<span class='arrow'></span></a>";
            }
            elseif($arrows1 == end($menu) && $arrowed1 == false)
            {
                $arrowed1 = true;
                reset($menu);
                return "</a>";
            }
        }

    }

    private static function activeCheck($data){
        // dd($array);
        // dd(self::$path);
        // foreach($array as $key => $value){
            if($data['resource'] == self::$path){
                return "active";
            }

            if($data['resource'] == '#'){

                $active = Menu::where('resource',self::$path)->first();
                if($data['menuid'] == $active->parent){
                    return "active open";
                }
            }
        // }

        return "";
    }

    private static function activeCheckChild($keywords, $array){
        
        foreach($array as $key => $value){
            if($value->id == $keywords){
                return "active";
            }
        }

        return "";
    }

    private static function generateActive($selected){

        $activeMenu     = [];

        $loop       = true;
        $counter    = 0;
        // while($loop){

            $active = Menu::where('resource',$selected)->first();

            $activeMenu[$counter] = $active;
            $counter++;

        //     if(!empty($active)){
        //         if($active->parent == 0){
        //             $loop   =  false; 
        //         }
        //         else $selected = $active->resource;
        //     }
        // }

        return $activeMenu;
    }

    public static function generatetable($menu){
        foreach($menu as $key => $value){

            if($value->level == 0){
                $menu0[$value->id]["menuid"]  = $value->id;
                $menu0[$value->id]["name"]    = $value->menuname;
                $menu0[$value->id]["parent"]  = $value->parent;
                $menu0[$value->id]["urutan"]  = $value->level;
                $menu0[$value->id]["akses"]   = $value->akses_id;
                $menu0[$value->id]["accessmenuid"]  = $value->accessmenuid;
            }
            
            elseif($value->level == 1){
                $menu1[$value->id]["menuid"]  = $value->id;
                $menu1[$value->id]["name"]    = $value->menuname;
                $menu1[$value->id]["parent"]  = $value->parent;
                $menu1[$value->id]["urutan"]  = $value->level;
                $menu1[$value->id]["akses"]   = $value->akses_id;
                $menu1[$value->id]["accessmenuid"]  = $value->accessmenuid;
            }

        }

        if(!empty($menu1)){
            uasort($menu1, function($a, $b) {

            $a1 = $a["name"];
            $b1 = $b["name"];

            $out = strcasecmp($a1,$b1);
                if($out == 0){ return  0;}
                if($out >  0){ return  1;}
                if($out <  0){ return -1;}
            });
        }

        $menutable  = [];
        $i = 0;
        if(!empty($menu0)){
            foreach ($menu0 as $key0 => $value0) {
                $menutable[$i]  = $value0;
                
                unset($menu0[$key0]);

                if(!empty($menu1)){
                    foreach ($menu1 as $key1 => $value1){
                        
                        if ($value1["parent"] == $key0) {
                            $i++;
                            $menutable[$i]  = $value1;
                            $menutable[$i]['name']  = "&boxur; &nbsp;&nbsp;" . $menutable[$i]['name'];

                            unset($menu1[$key1]);
                        }

                    }
                }

                $i++;
            }
        }

        if(!empty($menu1)){
            $menu1  = array_values($menu1);

            foreach ($menu1 as $key1 => $value1){
                $menutable[$i]  = $value1;

                unset($menu1[$key1]);
                $i++;
            }
        }

        return $menutable;
    }
}