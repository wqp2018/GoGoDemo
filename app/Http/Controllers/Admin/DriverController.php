<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/6
 * Time: 17:20
 */

namespace App\Http\Controllers\Admin;

use App\Model\DriverModel;
use DB;
use Illuminate\Http\Request;

class DriverController extends BaseController{


    public function getList(Request $request){
        $this->markPage();
        $keyword = $request->get('keyword', "");
        $builder = DB::table('driver');

        if ($keyword){
            $like_str = sprintf("%%%s%%", $keyword);
            $builder->where('name', 'like', $like_str);
        }

        $list = $builder->paginate(env('PAGE_SIZE'));

        return view('Admin.driver.list', compact('list'))
            ->with('keyword', $keyword);
    }

    public function getForm(Request $request){
        $id = $request->get('id', 0);

        $data = [];
        if ($id != 0){
            $data = DB::table('driver')
                ->where('id', $id)
                ->first();
        }

        return view('Admin.driver.form', compact('data'));
    }

    public function postForm(Request $request){
        $data = $request->all();

        $driverModel = new DriverModel();
        if ($err = $driverModel->validator($data)->first()){
            return $this->ajaxFail("", $err);
        }
        $is_phone = formatPhoneNumber($data['phone']);
        if (!$is_phone){
            return $this->ajaxFail("", "请输入正确的手机格式");
        }

        $id = $data['id'];
        unset($data['id']);
        $data['pass_word'] = \Crypt::encrypt($data['pass_word']);

        if ($id != 0){
            $op = "修改";
            $success = DB::table('driver')->where('id', $id)->update($data);
        }else{
            $op = "新增";
            $success = DB::table('driver')->insert($data);
        }

        if ($success){
            return $this->ajaxSuccess("", "{$op}成功", $this->getMarkPage());
        }
        return $this->ajaxFail("", "{$op}失败");
    }

}