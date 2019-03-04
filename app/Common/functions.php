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

// 获取随机数
function getRandNum($size){
    $num = "";
    for ($i = 0; $i < $size; $i++){
        $num .= rand(0,9);
    }

    return $num;
}

// 检验手机格式
function formatPhoneNumber($phone){
    $format = "/^1[34578]\d{9}$/";
    if (preg_match($format, $phone)){
        return true;
    }
    return false;
}

// 有空值，返回true
function hasEmpty($data = []){
    foreach ($data as $k => $v){
        if (empty($v)){
            return true;
        }
    }
    return false;
}

// 均为空，返回true
function allEmpty($data = []){
    foreach ($data as $k => $v){
        if (!empty($v)){
            return false;
        }
    }
    return true;
}

function timeStrToTime($str){
    $time = explode(":", $str);
    if (count($time) != 2){
        throw new \Exception("时间格式有误");
    }
    try{
        $minutes = intval($time[0]) * 60 + intval($time[1]);
    }catch (\Exception $e){
        throw new \Exception("时间格式有误");
    }
    return $minutes;
}

if (!function_exists("calculate_delivery_fee")){
    function calculate_delivery_fee($distance = 0){
        // 1000 米收一块钱
        $fee = ceil($distance / 1000);
        return $fee;
    }
}

if (!function_exists("pageSelect")){
    function pageSelect(\Illuminate\Database\Query\Builder $builder, $page_size = 0){
        $result = $builder->paginate($page_size);

        $list['data'] = $result->items();
        $list['total'] = $result->total();
        $list['currentPage'] = $result->currentPage();
        $list['page'] = $result->render();
        return $list;
    }
}