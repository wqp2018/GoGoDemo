<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/31
 * Time: 23:29
 */

namespace App\Service;

use DB;

class OrderService{

    public function acceptOrder($order_id){
        $success = DB::table('order')
            ->where('id', $order_id)
            ->update([
                "driver_id" => 0,
                "order_status" => 1,
                "update_time" => time()
            ]);

        if ($success){
            return true;
        }

        return false;
    }

    public function refuseOrder($order_id){
        // 先判断是否有骑手
        $order = DB::table('order')->find($order_id);
        if ($order['driver_id'] != 0){
            return false;
        }
        DB::beginTransaction();
        $success = DB::table('order')
            ->where('id', $order_id)
            ->update([
                "order_status" => 0,
                "update_time" => time()
            ]);

        $order_item = DB::table('order_item')
            ->where('order_id', $order_id)
            ->get();

        foreach ($order_item as $k => $v){
            // 减少店家的销量
             DB::table('store')
                 ->where('id', $order['store_id'])
                 ->decrement('total_sale', $v['count_number']);

             // 增加食物的库存
             DB::table('food')
                 ->where('id', $v['food_id'])
                 ->increment('inventory', $v['count_number']);

             // 减少食物的销量
            DB::table('food')
                ->where('id', $v['food_id'])
                ->decrement('total_sale', $v['count_number']);
        }

        $this->checkOrderTime($order_id);
        if ($success){
            $push_service = new PushService();
            $push_service->pushUserMessage($order['user_id'], "系统消息：您的订单编号【{$order['id']}】已取消");
            DB::commit();
            return true;
        }

        DB::rollBack();
        return false;
    }

    // 拒单时检查订单是否超过了自动取消时间
    public function checkOrderTime($order_id){
        // 自动拒单时长
        $refuse_time_long = intval(get_db_config('auto_refuse_order_time', 30)) * 60;

        $order = DB::table('order')->find($order_id);
        $refuse_time = $refuse_time_long + $order['create_time'];
        if (time() > $refuse_time){
            DB::table('order')
                ->where('id', $order)
                ->update([
                    "order_status" => 6,
                    "update_time" => time()
                ]);
        }
    }

}