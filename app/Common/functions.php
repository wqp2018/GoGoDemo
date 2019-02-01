<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/20
 * Time: 1:14
 */

function resetMenus(){
    $menus = DB::table('menus')->get();

    $parent_menus = DB::table('menus')
        ->where('status',1)
        ->where('parent_id',0)
        ->orderBy('sort_index','asc')
        ->get();

    Cache::put("ParentMenus",$parent_menus,env('CACHE_MINUTES',10));
    Cache::put("menus", $menus, env("CACHE_MINUTES", 10));
}

function set_db_config($name, $value, $remark = ""){
    $exist = DB::table('config')->where("name", $name)->first();

    if ($exist){
        DB::table('config')->where("name", $name)->update([
            "value" => json_encode($value),
            "remark" => $remark
        ]);
    }else{
        DB::table('config')->insert([
            "name" => $name,
            "value" => $value,
            "remark" => $remark
        ]);
    }
    \Cache::add($name, $value, env("CACHE_MINUTES"));
}

function get_db_config($name){
    $result = \Cache::get($name);
    if ($result){
        return $result;
    }
    $result = DB::table('config')->where('name', $name)->value("value");
    \Cache::add($name, $result, env("CACHE_MINUTES"));
    return $result;
}