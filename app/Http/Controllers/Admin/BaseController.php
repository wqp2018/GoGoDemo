<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Input;

class BaseController extends Controller
{
    public function __construct()
    {
        $this->getAllMenus();
    }

    // 获取总菜单
    public function getAllMenus(){
        $menus = Cache::get('menus');
        if (!$menus){
            $menus = DB::table('menus')->get();
            Cache::put("menus", $menus, env("CACHE_MINUTES", 10));
        }
    }

    public function getIndex(){
        $this->markPage();
        return redirect('/User/list');
    }

    //获取当前请求的URL
    public function markPage(){
        $url = \request()->url();

        Cache::forever('current_url',$url);
    }

    //获取缓存中的URL
    public function getMarkPage(){
        $url = Cache::get('current_url');

        return $url;
    }

    public function ajaxSuccess($data = [],$message = '',$url = ""){
        $return['status'] = 1;
        $return['info'] = $data;
        $return['message'] = $message;
        $return['url'] = $url;

        return $return;
    }

    public function ajaxFail($data = [],$message = ''){
        $return['status'] = 0;
        $return['info'] = $data;
        $return['message'] = $message;

        return $return;
    }

    //获取顶级菜单
    public function getMenus(){
        $menus = Cache::get('ParentMenus');
        if (!$menus){

        }

        return $this->ajaxSuccess($menus);
    }

    // 获取顶级菜单下面的子菜单
    public function getChildrenMenus(Request $request){
        $parent_id = $request->get('parent_id', 0);

        // 若是pid为空， 直接返回空
        if ($parent_id == 0){
            return $this->ajaxSuccess();
        }

        $menus = DB::table('menus')
            ->where('parent_id', $parent_id)
            ->where('status',1)
            ->get();

        return $this->ajaxSuccess($menus);
    }

    //修改状态
    public function postStatus(Request $request){
        $id = $request->get('id');
        $ids = is_array($id) ? $id : [$id];

        $status = $request->get('status');
        $mod = $request->get('mod');

        $success = DB::table($mod)
            ->whereIn('id',$ids)
            ->update([
               "status" => $status,
               "update_time" => time()
            ]);

        if ($success){
            return $this->ajaxSuccess(null,"修改状态成功",$this->getMarkPage());
        }
        return $this->ajaxFail('null','修改状态失败');
    }

}
