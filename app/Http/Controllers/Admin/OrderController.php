<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/29
 * Time: 5:53
 */

namespace App\Http\Controllers\Admin;

use App\Service\OrderService;
use App\Service\PushService;
use Illuminate\Http\Request;
use DB;

class OrderController extends BaseController{

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

    public function getList(Request $request){
        $keyword = $request->get('keyword', "");
        $cancel_order = intval($request->get('cancel_order', 0));
        $complete_order = intval($request->get('complete_order', 0));

        $select_time = $request->get('select_time');
        $begin_time = strtotime($select_time['begin_time'] ?? date('Y-m-d'));
        $end_time = strtotime($select_time['end_time'] ?? date("Y-m-d", strtotime("+1 day"))) + 24 * 60 * 60;

        $builder = DB::table('order as o')
            ->leftJoin('store as s', 'o.store_id', '=', 's.id')
            ->leftJoin('driver as d', 'o.driver_id', '=', 'd.id');

        if ($complete_order != 0){
            $builder->where('o.order_status', 4);
        }

        if ($cancel_order != 0){
            $builder->where('o.order_status', 6);
        }

        if ($keyword){
            $like_str = sprintf("%%%s%%", $keyword);
            $user_id = DB::table('user')
                ->where('name', 'like', $like_str)
                ->pluck('id');
            $store_id = DB::table('store')
                ->where('name', 'like', $like_str)
                ->pluck('id');

            $builder->where(function ($query) use($user_id, $store_id){
                $query->whereIn('user_id', $user_id)->orWhereIn('store_id', $store_id);
            });
        }

        $builder->where('o.create_time', '>', $begin_time)
            ->where('o.create_time', '<', $end_time);

        $builder->select(['o.*', 's.name as store_name', 'd.name as driver_name', 'd.phone as driver_phone'])->orderBy('create_time', 'desc');

        $list = pageSelect($builder, 10);

        foreach ($list['data'] as $k => $v){
            $list['data'][$k]['order_status_ch'] = $this->order_status[$v['order_status']];
        }

        return view('Admin.order.list')
            ->with('keyword', $keyword)
            ->with('list', $list)
            ->with('select_time', $select_time)
            ->with('cancel_order', $cancel_order)
            ->with('complete_order', $complete_order);
    }

    public function acceptOrder(Request $request){
        $order_id = $request->get('order_id', 0);
        if ($order_id == 0){
            return $this->ajaxFail("", "无法正确获得订单信息");
        }
        $order_service = new OrderService();
        $success = $order_service->acceptOrder($order_id);
        if ($success == true){
            return $this->ajaxSuccess("", "接单成功");
        }
        return $this->ajaxFail("", "接单发生错误");
    }

    public function refuseOrder(Request $request){
        $order_id = $request->get('order_id', 0);
        if ($order_id == 0){
            return $this->ajaxFail("", "无法正确获得订单信息");
        }
        $order_service = new OrderService();
        $success = $order_service->refuseOrder($order_id);
        if ($success == true){
            return $this->ajaxSuccess("", "拒单成功");
        }
        return $this->ajaxFail("", "拒单发生错误");
    }

    public function cancelOrderList(Request $request){
        $keyword = $request->get('keyword', "");

        $select_time = $request->get('select_time');
        if (!$select_time['begin_time']){
            $select_time['begin_time'] = date('Y-m-d', time());
            $select_time['end_time'] = date('Y-m-d', time());
        }

        $begin_time = strtotime($select_time['begin_time']);
        $end_time = strtotime($select_time['end_time']) + 24 * 60 * 60;

        $builder = DB::table('order as o')
            ->leftJoin('store as s', 'o.store_id', '=', 's.id')
            ->leftJoin('user as u', 'o.user_id', '=', 'u.id')
            ->leftJoin('order_cancel_remark as ocr', 'o.id', '=', 'ocr.order_id')
            ->where('o.order_status', 5);

        if ($keyword){
            $like_str = sprintf("%%%s%%", $keyword);
            $user_id = DB::table('user')
                ->where('name', 'like', $like_str)
                ->pluck('id');
            $store_id = DB::table('store')
                ->where('name', 'like', $like_str)
                ->pluck('id');

            $builder->where(function ($query) use($user_id, $store_id){
                $query->whereIn('user_id', $user_id)->orWhereIn('store_id', $store_id);
            });
        }

        $builder->where('o.create_time', '>', $begin_time)
            ->where('o.create_time', '<', $end_time);

        $builder->select(['o.*', 's.name as store_name',
            'ocr.role', 'ocr.remark as cancel_remark',
            'u.name as user_name', 'u.phone as user_phone']);

        $list = pageSelect($builder, 10);

        return view('Admin.order.cancel_list')
            ->with('keyword', $keyword)
            ->with('list', $list)
            ->with('select_time', $select_time);
    }

    public function allowCancelOrder(Request $request){
        $order_id = $request->get('order_id', 0);
        if ($order_id == 0){
            return $this->ajaxFail("", "无法正确获得订单信息");
        }
        $order_status = DB::table('order')->where('id', $order_id)->value('order_status');
        if ($order_status != 5){
            return $this->ajaxFail("", "此订单非请求取消订单，操作失败。");
        }
        $order_service = new OrderService();
        $success = $order_service->refuseOrder($order_id);

        if ($success){
            return $this->ajaxSuccess("", "操作成功，即将跳转。");
        }

        return $this->ajaxFail("", "操作出错。");
    }

    public function orderDetail(Request $request){
        $order_id = $request->get('id', 0);
        if ($order_id == 0){
            return $this->ajaxFail("", "无法正确获得订单信息");
        }

        $data = DB::table('order')
            ->leftJoin('user', 'order.user_id', '=', 'user.id')
            ->leftJoin('store', 'order.store_id', '=', 'store.id')
            ->where('order.id', $order_id)
            ->select(['order.*', 'store.name as store_name', 'store.phone as store_phone',
                'user.name as user_name', 'user.phone as user_phone'
            ])
            ->first();

        $data['food'] = DB::table('order_item as oi')
            ->leftJoin('food', 'food.id', '=', 'oi.food_id')
            ->where('order_id', $data['id'])
            ->select(['oi.*', 'food.name'])
            ->get()->toArray();

        return view('Admin.order.detail', compact('data'))->with('order_status', $this->order_status);

    }
}