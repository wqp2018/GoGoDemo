<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/23
 * Time: 23:37
 */
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use DB;

class CityController extends BaseController{

    // 获取一级城市
    public function getFirstLevelCity(){
        $data = DB::table('city')
            ->where('parent_id', 0)
            ->get();

        return $this->ajaxSuccess($data);
    }

    // 获取下一级城市
    /* 需要传入上一级城市ID */
    public function getNextLevelCity(Request $request){
        $parent_id = $request->get('parent_id', 0);
        if ($parent_id == 0){
            return $this->ajaxSuccess();
        }
        $data = DB::table('city')
            ->where('parent_id', $parent_id)
            ->get();

        return $this->ajaxSuccess($data);
    }
}