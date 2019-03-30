<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/11
 * Time: 19:22
 */

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Api\BaseApiController;
use App\Service\StoreService;
use App\Service\UserService;
use Illuminate\Http\Request;
use DB;

class OrderApiController extends BaseApiController{

    protected $userService;

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

    // 支付方式， 1 - 现金， 2 - 支付宝
    protected $pay_type = [
        1 => "货到付款",
        2 => "支付宝"
    ];

    public function __construct(Request $request)
    {
        parent::__construct();
        $this->userService = new UserService();
    }

    public function beforeOrdering(Request $request){
        try{
            $user = $this->userService->checkUserLogin();
        }catch (\Exception $e){
            return $e->getMessage();
        }
        $store_id = $request->get('store_id', 0);
        $select_foods = json_decode($request->get('select_food', ""), true);
        $address_id = $request->get('address_id', 0);

        if ($address_id == 0){
            // 没有特别选择，使用用户默认地址
            $address = DB::table('user_address')
                ->where('user_id', $user['id'])
                ->where('id', $user['default_address_id'])
                ->first();
        }else{
            // 特别选择的地址
            $address = DB::table('user_address')
                ->where('user_id', $user['id'])
                ->where('id', $address_id)
                ->first();
        }

        if ($store_id == 0){
            return view('error')->with('msg', '缺少必要的参数');
        }
        $field = sprintf('get_distance(lat,lng,%s,%s) as distance', $address['lat'], $address['lng']);
        $store = DB::table('store')
            ->where('id', $store_id)
            ->selectRaw($field)
            ->addSelect('store.*')
            ->first();

        $check_order = $this->checkOrder($store, $select_foods);

        if ($check_order != ""){
            return view('error')->with('msg', $check_order);
        }

        $delivery_fee = calculate_delivery_fee($store['distance']);
        $expect_delivery_time = date("H:i" ,get_expect_delivery_time($store['distance']));

        return view('Api.store.orderDialog',compact('store', 'select_foods', 'address', 'delivery_fee', 'expect_delivery_time'));
    }

    // 下单检查
    private function checkOrder($store, $select_foods){
        if (!$store){
            return '不存在该店家，请重新检查';
        }
        if($store['status'] == 0){
            return '该店家已不在营业';
        }

        if (empty($select_foods)){
            return '请先选择食物';
        }

        // 检查店家是否可下单
        $store_service = new StoreService();
        $checkStore = $store_service->checkStoreAbnormalStatus($store);
        if ($checkStore['status'] == 1){
            return '所选地址超出了配送范围';
        }elseif ($checkStore['status']== 2){
            return  '店家非营业时间内不能下单';
        }

        // 检查货物库存是否充足, 货物状态是否正常
        $checkResult = $this->checkInventory($select_foods);

        if ($checkResult !== true){
            return $checkResult;
        }

        return "";
    }

    // 检查库存
    private function checkInventory($select_foods){
        $food_collect = collect($select_foods);
        $food_ids = $food_collect->pluck('id')->toArray();
        $food_list = DB::table('food')
            ->whereIn('id', $food_ids)
            ->select(['id', 'name', 'inventory', 'total_sale', 'status'])
            ->get();
        foreach ($food_list as $k => $v){
            if ($v['inventory'] < $food_collect->where('id', '=', $v['id'])->values()->first()['count']){
                return "商品【{$v['name']}】库存不足，请确认后再重新下单";
            }
            if ($v['status'] == 0){
                return "商品【{$v['name']}】该商品已下架，请确认后再重新下单";
            }
        }
        return true;
    }

    public function ordering(Request $request){
        try{
            $user = $this->userService->checkUserLogin();
        }catch (\Exception $e){
            return $e->getMessage();
        }
        $store_id = $request->get('store_id', 0);
        $select_foods = json_decode($request->get('select_food', ""), true);
        $address_id = $request->get('address_id', 0);
        $pay_type = $request->get('pay_type', 1);
        $remark = $request->get('remark', "");

        if ($address_id == 0){
            // 没有特别选择，使用用户默认地址
            $address = DB::table('user_address')
                ->where('user_id', $user['id'])
                ->where('id', $user['default_address_id'])
                ->first();
        }else{
            // 特别选择的地址
            $address = DB::table('user_address')
                ->where('user_id', $user['id'])
                ->where('id', $address_id)
                ->first();
        }

        if ($store_id == 0){
            return $this->apiFail("", "缺少必要的参数");
        }
        $field = sprintf('get_distance(lat,lng,%s,%s) as distance', $address['lat'], $address['lng']);
        $store = DB::table('store')
            ->where('id', $store_id)
            ->selectRaw($field)
            ->addSelect('store.*')
            ->first();

        $check_order = $this->checkOrder($store, $select_foods);
        if ($check_order != ""){
            return $this->apiFail("", $check_order);
        }

        $food_ids = array_column($select_foods, 'id');
        $foods = collect(DB::table('food')
            ->whereIn('id', $food_ids)
            ->select(['id', 'price', 'name'])
            ->get());

        $total_price = 0;
        foreach ($select_foods as $k => $v){
            $total_price += $v['count'] * intval($foods->where('id', $v['id'])->first()['price']);
            // 使用数据库中的价格
            $select_foods[$k]['price'] = intval($foods->where('id', $v['id'])->first()['price']);
        }

        $delivery_fee = intval(calculate_delivery_fee($store['distance']));
        $total_price += $delivery_fee;
        $actual_payment = $total_price;

        $expect_delivery_time_seconds = get_expect_delivery_time($store['distance']);

        $order_status = -1;
        if ($pay_type == 1){
            $order_status = 0;
        }
        DB::beginTransaction();
        $order = [
            "user_id" => $user['id'],
            "store_id" => $store['id'],
            "lat" => $address['lat'],
            "lng" => $address['lng'],
            "store_lat" => $store['lat'],
            "store_lng" => $store['lng'],
            "pay_type" => $pay_type,
            "order_status" => $order_status,
            "total_price" => $total_price,
            "actual_payment" => $actual_payment,
            "delivery_fee" => $delivery_fee,
            "address_json" => json_encode($address),
            "store_address_json" => $store['address'],
            "expect_delivery_time" => $expect_delivery_time_seconds,
            "remark" => $remark,
            "create_time" => time(),
            "update_time" => time()
        ];

        $order_id = DB::table('order')->insertGetId($order);

        if (!$order_id){
            DB::rollBack();
            return $this->apiFail("", "下单失败");
        }

        $order_item = [];
        foreach ($select_foods as $k => $v){
            $order_item[$k] = [
                "order_id" => $order_id,
                "food_id" => $v['id'],
                "price" => $v['price'],
                "count_number" => $v['count'],
                "create_time" => time()
            ];

            // 减商品库存
            $reduce_result = $this->reduceFoodInventory($v['id'], $v['count']);

            // 增加销售数量
            $insert_result = $this->insertFoodSale($v['id'], $v['count'], $store_id);

            if (!$reduce_result || !$insert_result){
                DB::rollBack();
                return $this->apiFail("", "下单失败");
            }
        }

        $order_item_success = DB::table('order_item')->insert($order_item);

        if (!$order_item_success){
            DB::rollBack();
            return $this->apiFail("", "下单失败");
        }

        DB::commit();
        return $this->apiSuccess("","下单成功", url('OrderApi/orderDetail')."?order_id={$order_id}");
    }

    private function reduceFoodInventory($food_id, $count = 0){
        $inventory = DB::table('food')
            ->where('id', $food_id)
            ->value('inventory');

        $end_count = $inventory - $count;
        if ($end_count < 0){
            return false;
        }

        $success = DB::table('food')
            ->where('id', $food_id)
            ->update([
                'inventory' => $end_count
            ]);
        if (!$success){
            return false;
        }
        return true;
    }

    private function insertFoodSale($food_id, $count = 0, $store_id = 0){
        $success = DB::table('food')
            ->where('id', $food_id)
            ->increment('total_sale', $count);

        $store_success = DB::table('store')
            ->where('id', $store_id)
            ->increment('total_sale', $count);

        if (!$success || !$store_success){
            return false;
        }
        return true;
    }

    public function selectAddress(Request $request){
        try{
            $user = $this->userService->checkUserLogin();
        }catch (\Exception $e){
            return $e->getMessage();
        }
        $select_address_id = $request->get('address_id', 0);

        $address_list = DB::table('user_address')
            ->where('user_id', $user['id'])
            ->get();

        return view('Api.user.addressDialog',compact('address_list', 'user', 'select_address_id'));
    }


    // 选择地址
    public function chooseAddress(Request $request){
        $address_id = $request->get('address_id', 0);

        $address = DB::table('user_address')
            ->where('id', $address_id)
            ->first();

        return $this->apiSuccess($address);
    }

    public function orderList(){
        return view('Api.user.order_list');
    }

    // 用户订单列表
    public function orderListAjax(Request $request){
        try{
            $user = $this->userService->checkUserLogin();
        }catch (\Exception $e){
            return $e->getMessage();
        }
        $page = $request->get('page', 0);

        $list = DB::table('order')
            ->leftJoin('store', 'order.store_id', '=', 'store.id')
            ->where('user_id', $user['id'])
            ->offset($page)
            ->limit(10)
            ->select(['order.*', 'store.name', 'store.avatar'])
            ->get()
            ->toArray();

        foreach ($list as $k => $v){
            $list[$k]['items'] = DB::table('order_item as oi')
                ->leftJoin('food', 'oi.food_id', '=', 'food.id')
                ->where('order_id', $v['id'])
                ->select(['oi.*', 'food.name'])
                ->get()->toArray();

            $list[$k]['create_time'] = date("Y-m-d H:i", $v['create_time']);
            $list[$k]['order_status_ch'] = $this->order_status[$v['order_status']];
        }

        $data['order_list'] = $list;
        $data['all_count'] = DB::table('order')
            ->where('user_id', $user['id'])
            ->count(['id']);

        return $this->apiSuccess($data);
    }


    // 订单详情
    public function orderDetail(Request $request){
        try{
            $user = $this->userService->checkUserLogin();
        }catch (\Exception $e){
            return $e->getMessage();
        }
        $order_id = $request->get('order_id', 0);
        if ($order_id == 0){
            return view('error')->with('msg', "请先选择一个订单再查看详情");
        }

        $order = DB::table('order')
            ->leftJoin('store', 'order.store_id', '=', 'store.id')
            ->where('user_id', $user['id'])
            ->where('order.id', $order_id)
            ->select(['order.*', 'store.name', 'store.avatar', 'store.phone'])
            ->first();
        $order['address_json'] = json_decode($order['address_json'], true);

        $order['order_status'] = $this->order_status[$order['order_status']];
        $order['pay_type'] = $this->pay_type[$order['pay_type']];

        $order['items'] = DB::table('order_item as oi')
            ->leftJoin('food', 'oi.food_id', '=', 'food.id')
            ->where('order_id', $order['id'])
            ->select(['oi.*', 'food.name'])
            ->get()->toArray();

        return view('Api.user.order_detail', compact("order"));
    }

    public function cancelOrder(Request $request){
        try{
            $user = $this->userService->checkUserLogin();
        }catch (\Exception $e){
            return $e->getMessage();
        }
        $order_id = intval($request->get('order_id', 0));
        $order = DB::table('order')->where('id', $order_id)->where('user_id', $user['id'])->first();
        if ($order_id == 0 || !$order){
            return $this->apiFail("", "发生错误，不能正确获取订单");
        }

        if (in_array($order['order_status'], [5,6])){
            return $this->apiFail("", "订单已取消或者已在取消审核中，本次操作失败！");
        }

        $success = DB::table('order')
            ->where('id', $order_id)
            ->update([
               "order_status" => 5,
                "update_time" => time()
            ]);
        if (!$success){
            return $this->apiFail("", "订单取消失败");
        }

        return $this->apiSuccess("", "订单取消成功");

    }
}