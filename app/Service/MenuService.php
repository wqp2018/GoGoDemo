<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/20
 * Time: 2:06
 */

namespace App\Service;

class MenuService{

    //  无限级菜单
    public function getMenusRelation(){
        $menus = \Cache::get('menus');

        $newMenus = $this->reSorts($menus, 0);

        return $newMenus;
    }

    private function reSorts($data, $pid = 0){
        $ret = array();
        foreach ($data as $k => $v) {
            if($v['parent_id'] == $pid) {
                $v['children'] = $this->reSorts($data, $v['id']);
                $ret[] = $v;
            }
        }
        return $ret;
    }

}