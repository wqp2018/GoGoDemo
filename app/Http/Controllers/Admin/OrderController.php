<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/29
 * Time: 5:53
 */

namespace App\Http\Controllers\Admin;

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
        $begin_time = strtotime($select_time['begin_time']);
        $end_time = strtotime($select_time['end_time']) + 24 * 60 * 60;

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

        $builder->select(['o.*', 's.name as store_name', 'd.name as driver_name', 'd.phone as driver_phone']);

        $list = pageSelect($builder, 10);

        foreach ($list['data'] as $k => $v){
            $list['data'][$k]['order_status_ch'] = $this->order_status[$v['order_status']];
        }

        return view('Admin.order.list')
            ->with('list', $list)
            ->with('select_time', $select_time)
            ->with('cancel_order', $cancel_order)
            ->with('complete_order', $complete_order);
    }


}