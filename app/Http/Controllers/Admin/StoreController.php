<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/23
 * Time: 23:37
 */
namespace App\Http\Controllers\Admin;

use App\Model\StoreModel;
use Illuminate\Http\Request;
use DB;

class StoreController extends BaseController{
    protected $pay_type = [
        1 => "现金",
        2 => "支付宝"
    ];

    public function getList(Request $request){
        $this->markPage();
        $keyword = $request->get('keyword');

        $builder = DB::table('store');

        if ($keyword){
            $like_str = sprintf("%%%s%%",$keyword);
            $builder->where('name','like',$like_str);
        }

        $list = $builder
            ->orderBy('status', 'desc')
            ->orderBy('id', 'asc')
            ->paginate(env("PAGE_SIZE",10));

        return view('Admin.store.list',compact('list','keyword'));
    }

    public function getForm(Request $request){
        \View::share("pay_type", $this->pay_type);
        $id = $request->get('id', 0);

        $data = [];
        $data['pay_type'] = [];
        $data['next_city'] = 0;
        $data['next_next_city'] = 0;

        if ($id != 0){
            $data = DB::table('store')->find($id);
            $business_time = $data['business_time'];
            unset($data['business_time']);
            $first_business = explode("、", $business_time)[0];
            $second_business = explode("、", $business_time)[1] ?? null;

            $data['business_time']['first_begin_time'] = explode("-", $first_business)[0];
            $data['business_time']['first_end_time'] = explode("-", $first_business)[1];
            if ($second_business != null){
                $data['business_time']['second_begin_time'] = explode("-", $second_business)[0];
                $data['business_time']['second_end_time'] = explode("-", $second_business)[1];
            }
            $data['pay_type'] = explode(",", $data['pay_type']);

            // 获取该城市城市上一级元素
            $result = DB::table('city as c')
                ->leftJoin('city as c1', 'c.parent_id', '=', 'c1.id')
                ->leftJoin('city as c2', 'c1.parent_id', '=', 'c2.id')
                ->where('c.id', $data['city_id'])
                ->select(['c1.id as next_city', 'c2.id as next_next_city'])
                ->first();

            $data['next_city'] = $result['next_city'];
            $data['next_next_city'] = $result['next_next_city'];
        }
        $data['city'] = DB::table('city')->where('parent_id', 0)->get();

        return view('Admin.store.form', compact('data'));
    }

    public function postForm(Request $request){
        $data = $request->all();

        $store = new StoreModel();
        if ($err = $store->validator($data)->first()){
            return $this->ajaxFail("",$err);
        }

        if (!formatPhoneNumber($data['phone'])){
            return $this->ajaxFail("", "手机号码格式不正确，请检查后重新输入");
        }

        $id = $data['id'];
        unset($data['id']);
        $business_time = $data['business_time'];
        unset($data['business_time']);

        if (allEmpty($business_time)){
            return $this->ajaxFail("", "必须要有一个营业时间");
        }

        if (($business_time['first_begin_time'] == null && $business_time['first_end_time'] != null) ||
            ($business_time['second_begin_time'] == null && $business_time['second_end_time'] != null)){
            return $this->ajaxFail("","请检查营业时间是否选择有误");
        }

        try{
            $first_business = $this->checkTimeCorrect($business_time['first_begin_time'], $business_time['first_end_time']);
            $second_business = $this->checkTimeCorrect($business_time['second_begin_time'], $business_time['second_end_time']);
            if (!$first_business || !$second_business){
                return $this->ajaxFail("", "结束营业时间不能小于开始营业时间");
            }
            $business_first['first_begin_time'] = $business_time['first_begin_time'];
            $business_first['first_end_time'] = $business_time['first_end_time'];
            $business_second['second_begin_time'] = $business_time['second_begin_time'];
            $business_second['second_end_time'] = $business_time['second_end_time'];

            // 如果第一个营业时间段时间为空，则取第二个时间段的值为第一个时间段
            if (allEmpty($business_first)){
                $business_first['first_begin_time'] = $business_time['second_begin_time'];
                $business_first['first_end_time'] = $business_time['second_end_time'];
                $business_second['second_begin_time'] = null;
                $business_second['second_end_time'] = null;
            }

            $checkTimeOverlap = $this->checkTimeOverlap($business_first, $business_second);
            if (!$checkTimeOverlap){
                return $this->ajaxFail("", "营业时间段有重叠，请检查。");
            }
        }catch (\Exception $e){
            return $this->ajaxFail("", $e->getMessage());
        }

        // 拼接营业时间
        $business_first_str = implode($business_first, "-");
        $business_second_str = implode($business_second, "-");
        if ($business_second_str == "-"){
            $data['business_time'] = $business_first_str;
        }else{
            $data['business_time'] = $business_first_str . "、" .$business_second_str;
        }
        if(!isset($data['pay_type'])){
            $data['pay_type'] = "";
        }else{
            $data['pay_type'] = implode($data['pay_type'], ",");
        }

        if ($id != 0){
            $op = "修改";
            $success = DB::table('store')->where('id', $id)->update($data);
        }else{
            $op = "新增";
            $success = DB::table('store')->insert($data);
        }

        if ($success){
            return $this->ajaxSuccess("", "{$op}成功，即将跳转", $this->getMarkPage());
        }
        return $this->ajaxFail("", "{$op}失败");
    }

    // 检查时间前后放置是否有误
    public function checkTimeCorrect($begin_time, $end_time){
        // 若均为空，则无需比较
        if (allEmpty([$begin_time, $end_time])){
            return true;
        }
        try{
            $begin_minutes = timeStrToTime($begin_time);
            $end_minutes = timeStrToTime($end_time);
        }catch (\Exception $e){
            throw $e;
        }
        if ($end_minutes <= $begin_minutes){
            return false;
        }
        return true;
    }

    // 检查营业时间是否重叠
    public function checkTimeOverlap($first_business_time, $second_business_time){
        // 若第二个营业时间为空，则直接返回
        if (hasEmpty($second_business_time)){
            return true;
        }
        try{
            $first_business_time_begin_minutes = timeStrToTime($first_business_time['first_begin_time']);
            $first_business_time_end_minutes = timeStrToTime($first_business_time['first_end_time']);
            $second_business_time_begin_minutes = timeStrToTime($second_business_time['second_begin_time']);
            $second_business_time_end_minutes = timeStrToTime($second_business_time['second_end_time']);

            // 第一个时间段小于第二个时间段
            if (($first_business_time_begin_minutes <= $second_business_time_begin_minutes && $second_business_time_begin_minutes < $first_business_time_end_minutes) ||
                ($first_business_time_begin_minutes <= $second_business_time_end_minutes && $second_business_time_end_minutes < $first_business_time_end_minutes)){
                return false;
            }

            // 第一个时间段大于第二个时间段
            if (($first_business_time_begin_minutes >= $second_business_time_begin_minutes && $first_business_time_begin_minutes < $second_business_time_end_minutes) ||
                ($first_business_time_end_minutes >= $second_business_time_begin_minutes && $second_business_time_end_minutes > $first_business_time_end_minutes)){
                return false;
            }
        }catch (\Exception $e){
            throw $e;
        }

        return true;
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