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

    // 获取二级菜单列表
    public function getSecondMenus(){
        $second_menus = Cache::get('second_menus');
        if ($second_menus == null){
            // 顶级菜单ID
            $top_menus = DB::table('menus')
                ->where('parent_id', 0)
                ->pluck('id');

            // 二级菜单ID 和 url
            $second_menus = DB::table('menus')
                ->whereIn('parent_id', $top_menus)
                ->select(['id', 'url'])
                ->get();

            Cache::put("second_menus", $second_menus, env("CACHE_MINUTES", 10));
        }

        return $second_menus;
    }

    // 获取顶级菜单下面的子菜单
    public function getChildrenMenus(Request $request){
        $parent_id = $request->get('parent_id', 0);

        // 若是pid为空， 直接返回用户管理页面
        if ($parent_id == 0){
            $parent_id = 1;
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
        $ids = explode(",", $id);

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
        return $this->ajaxFail(null,'修改状态失败');
    }

    // 获取当前url的二级菜单、最顶级菜单
    public function getSecondMenusUrl(Request $request){
        $url = $request->get('current_url');

        $response['second_url']  = $this->getCurrentUrlSecondUrl($url);
        $response['top_url']  = $this->getCurrentUrlTopUrl($response['second_url']);

        return $this->ajaxSuccess($response);
    }

    // 获取当前地址的第二阶父地址
    private function getCurrentUrlSecondUrl($url){
        $current_url = DB::table('menus')->where('url', $url)->first();

        // 获取当前url 的父菜单
        $parent_url = DB::table('menus')->where('id', $current_url['parent_id'])->first();

        // 父菜单没有父菜单时， 则是第二级菜单
        if ($parent_url['parent_id'] != 0){
            $current_second_url = $this->getCurrentUrlSecondUrl($parent_url['url']);
        }else{
            $current_second_url = $current_url['url'];
        }

        return $current_second_url;
    }

    // 获取当前地址的最顶阶父地址
    private function getCurrentUrlTopUrl($url){
        $parent = DB::table('menus')->where('url', $url)->first();
        if ($parent['parent_id'] == 0){
            return $parent['url'];
        }
        $parent_url = DB::table('menus')->find($parent['id']);

        return $parent_url['url'];
    }

}
