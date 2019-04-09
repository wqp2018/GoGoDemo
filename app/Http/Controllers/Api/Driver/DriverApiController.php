<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/4
 * Time: 1:25
 */

namespace App\Http\Controllers\Api\Driver;

use App\Service\OrderService;
use App\Service\PushService;
use DB;
use App\Http\Controllers\Api\BaseApiController;
use Illuminate\Http\Request;

class DriverApiController extends BaseApiController{

    //订单状态，-1 未支付，0-待接单，1-已接单，2-配送员已接单，3-配送中，4-订单已完成，5-取消中，待审核，6-已退款
    protected $order_status = [
        0 => "待接单",
        1 => "已接单",
        2 => "配送员已接单",
        3 => "配送中",
        4 => "订单已完成",
        5 => "取消中，待审核",
        6 => "已退款"
    ];

    private function getDriver(){
        $driver = \Session::get('driver');
        if ($driver == null){
            return false;
        }
        return $driver;
    }

    public function getIndex(){
        $driver = $this->getDriver();
        if ($driver === false){
            return "未登录或登录信息已失效。";
        }

        $order_list = DB::table('order as o')
            ->leftJoin('store as s', 'o.store_id', '=', 's.id')
            ->where('o.order_status', 1)
            ->orderBy('o.create_time', 'desc')
            ->select(['o.*', 's.name as store_name'])
            ->get()->toArray();

        $have_order = DB::table('order as o')
            ->leftJoin('store as s', 'o.store_id', '=', 's.id')
            ->where('o.driver_id', $driver['id'])
            ->orderBy('o.create_time', 'desc')
            ->select(['o.*', 's.name as store_name'])
            ->get()->toArray();

        $refuse_order_ids = DB::table('driver_refuse')
            ->where('driver_id', $driver['id'])
            ->pluck('order_id');

        $refuse_order = DB::table('order as o')
            ->leftJoin('store as s', 'o.store_id', '=', 's.id')
            ->whereIn('o.id', $refuse_order_ids)
            ->orderBy('o.create_time', 'desc')
            ->select(['o.*', 's.name as store_name'])
            ->get()->toArray();

        $this->getOrderFood($order_list);
        $this->getOrderFood($have_order);
        $this->getOrderFood($refuse_order);

        return view('Api.driver.index', compact('order_list', 'have_order', 'refuse_order'))->with('order_status', $this->order_status);
    }

    private function getOrderFood(&$orders){
        foreach ($orders as $k => $v){
            $food = DB::table('order_item')
                ->leftJoin('food', 'food.id', '=', 'order_item.food_id')
                ->where('order_id', $v['id'])
                ->select(['order_item.*', 'food.name'])
                ->get()->toArray();

            $orders[$k]['food'] = $food;
        }
    }

    public function acceptOrder(Request $request){
        $driver = $this->getDriver();
        if ($driver === false){
            return "未登录或登录信息已失效。";
        }

        $order_id = $request->get('order_id', 0);
        if ($order_id == 0){
            return $this->apiFail("", "请先选择订单");
        }

        $order = DB::table('order')->find($order_id);
        // 订单不是处于接单状态，则返回刷新
        if ($order['order_status'] != 1){
            return $this->apiSuccess("", "该订单信息已修改，即将刷新");
        }

        $success = DB::table('order')
            ->where('id', $order_id)
            ->update([
                "order_status" => 2,
                "driver_id" => $driver['id'],
                "update_time" => time()
            ]);
        if ($success){
            return $this->apiSuccess("", "接单成功");
        }

        return $this->apiFail("", "接单失败，发生不知名错误");

    }

    public function refuseOrder(Request $request)
    {
        $driver = $this->getDriver();
        if ($driver === false) {
            return $this->apiFail("", "未登录或登录信息已失效。");
        }

        $order_id = $request->get('order_id', 0);
        $order = DB::table('order')
            ->where('id', $order_id)
            ->where('driver_id', $driver['id'])
            ->first();

        if (!$order){
            return $this->apiFail("", "获取订单信息失败");
        }
        DB::beginTransaction();
        $success = DB::table('order')
            ->where('id', $order_id)
            ->update([
                "order_status" => 1,
               "driver_id" => 0,
               "update_time" => time()
            ]);
        $insert_success = DB::table('driver_refuse')
            ->insert([
              "driver_id" => $driver['id'],
              "order_id" => $order_id,
              "create_time" => time()
            ]);
        $order_server = new OrderService();
        $order_server->checkOrderTime($order_id);
        $driver_success = DB::table('driver')
            ->where('id', $driver['id'])
            ->increment('refuse_count', 1);
        $push_service = new PushService();
        $push_service->pushUserMessage($order['user_id'], "系统消息：您的订单编号【{$order['id']}】已取消");

        if ($success && $insert_success && $driver_success){
            DB::commit();
            return $this->apiSuccess("", "成功取消订单");
        }

        DB::rollBack();
        return $this->apiFail("", "取消订单失败");

    }

    public function finishOrder(Request $request){
        $driver = $this->getDriver();
        if ($driver === false) {
            return $this->apiFail("", "未登录或登录信息已失效。");
        }

        $order_id = $request->get('order_id', 0);
        $order = DB::table('order')
            ->where('id', $order_id)
            ->where('driver_id', $driver['id'])
            ->first();

        if (!$order){
            return $this->apiFail("", "获取订单信息失败");
        }

        $success = DB::table('order')->update([
           "order_status" => 4,
            "update_time" => time()
        ]);
        DB::table('driver')->where('id', $driver['id'])->increment('finish_count');
        if ($success){
            return $this->apiSuccess("", "订单已完成");
        }

        return $this->apiFail("", "操作失败");
    }

}