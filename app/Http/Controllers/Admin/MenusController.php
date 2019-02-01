<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/15
 * Time: 23:48
 */
namespace App\Http\Controllers\Admin;

use App\Model\MenusModel;
use App\Service\MenuService;
use DB;
use Illuminate\Http\Request;

class MenusController extends BaseController{

    protected $page = 20;

    //  获取设置菜单列表
    public function getList(Request $request){
        $this->markPage();
        $keyword = $request->get('keyword', '');
        $parent_id = $request->get('parent_id', 0);

        $builder = DB::table('menus')->where('parent_id', $parent_id);

        if ($keyword){
            $like_str = sprintf("%%%s%%", $keyword);
            $builder->where('name', 'like', $like_str);
        }
        $list = $builder->paginate($this->page);

        return view('menus.list', compact('list'));
    }

    public function getForm(Request $request){
        $id = $request->get('id', 0);

        $menusService = new MenuService();
        $menus = $menusService->getMenusRelation();
        $newMenus = [];
        $this->sortMenus($menus, $newMenus);
        $data = [];
        if ($id != 0){
            $data = DB::table('menus')->find($id);
        }

        return view('menus.form', compact('data', 'newMenus'));
    }

    public function postForm(MenusModel $menusModel, Request $request){
        $data = $request->all();

        if ($err = $menusModel->validator($data)->first()){
            return $this->ajaxFail("", $err);
        }

        $id = $data['id'];
        unset($data['id']);
        if ($id != 0){
            $op = "修改";
            $success = $menusModel::where('id', $id)->update($data);
        }else{
            $op = "新增";
            $success = $menusModel->save($data);
        }
        resetMenus();

        if ($success){
            return $this->ajaxSuccess("", $op."成功", $this->getMarkPage());
        }
        return $this->ajaxFail("", $op."失败");

    }

    private function sortMenus($menus, &$newMenus, $level = 0){
        $prefix = $this->getPrefix($level);
        foreach ($menus as $k => &$v){
            $v['name'] = $prefix.$v['name'];
            $newMenus[] = $v;
            if (count($v['children']) > 0){
                $level++;
                $this->sortMenus($v['children'], $newMenus, $level);
                $level--;
            }
        }
    }

    private function getPrefix($level){
        $prefix = "";
        $space = "";
        if ($level == 1){
            $prefix = "└-";
        }
        if ($level >= 2){
            for ($i = 0; $i < $level - 1; $i++){
                $space .= "&nbsp&nbsp&nbsp";
            }
            $prefix = $space."└-";
        }

        return $prefix;
    }

}