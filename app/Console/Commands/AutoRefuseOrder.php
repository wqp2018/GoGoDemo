<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/4
 * Time: 0:30
 */

namespace App\Console\Commands;

use App\Service\OrderService;
use Illuminate\Console\Command;
use DB;

class AutoRefuseOrder extends Command{

    protected $signature = 'auto_refuse_order';

    protected $description = '这是一个测试Laravel定时任务的描述';

    public function __construct(string $name = null)
    {
        parent::__construct($name);
    }

    public function handle(){
        $order_server = new OrderService();

        // 自动拒单时长
        $refuse_time_long = intval(get_db_config('auto_refuse_order_time', 30)) * 60;
        $now = time();

        $order_result = DB::table('order')
            ->where('order_status', 0)
            ->where('create_time', '<', $now - $refuse_time_long)
            ->get();

        foreach ($order_result as $k => $v){
            $result = $order_server->refuseOrder($v['id']);
            if ($result == false){
                \Log::info("订单号【{$v['id']}】拒单失败");
            }else{
                \Log::info("订单号【{$v['id']}】已自动拒单");
            }
        }
    }
}