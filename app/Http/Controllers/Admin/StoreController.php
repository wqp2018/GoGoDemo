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

class StoreController extends BaseController{

    public function getList(Request $request){
        $keyword = $request->get('keyword');

        $builder = DB::table('store');

        if ($keyword){
            $like_str = sprintf("%%%s%%",$keyword);
            $builder->where('name','like',$like_str);
        }

        $list = $builder->paginate(env("PAGE_SIZE",10));

        return view('Admin.store.list',compact('list','keyword'));
    }

    public function getForm(Request $request){
        $id = $request->get('id', 0);

        $data = [];

        if ($id != 0){
            $data = DB::table('store')->find($id);
        }

        return view('Admin.store.form', compact('data'));
    }

    public function postForm(Request $request){
        $data = $request->all();

        dd($data);
    }

    // 获取地图信息
    public function getMap(){
        return view('Dialog.map');
    }

    public function postStatus(Request $request)
    {
        return parent::postStatus($request); // TODO: Change the autogenerated stub
    }
}