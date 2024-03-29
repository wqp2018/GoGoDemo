<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/22
 * Time: 3:39
 */

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Api\BaseApiController;
use App\Model\UserAddressModel;
use App\Service\StoreService;
use App\Service\UserService;
use DB;
use Illuminate\Http\Request;

class UserApiController extends BaseApiController {
    protected $type = [
        "hot" => "热门店家",
        "recommended" => "推荐店家",
        "near" => "附近餐厅"
    ];

    protected $userService;
    protected $user;

    public function __construct(Request $request)
    {
        parent::__construct();
        $this->userService = new UserService();
    }

    // 用户首页
    public function getHomePage(){
        try{
            $user = $this->userService->checkUserLogin();
        }catch (\Exception $e){
            return $e->getMessage();
        }

        $store_service = new StoreService();
        $recommended_store = $store_service->getRecommendedStoreList(4, "", $user);
        $hot_store = $store_service->getHotStoreList(4, "", $user);

        return view('Api.user.index', compact('user','recommended_store', 'hot_store'));
    }

    public function getHotStore(Request $request){
        try{
            $user = $this->userService->checkUserLogin();
        }catch (\Exception $e){
            return $e->getMessage();
        }
        $keyword = $request->get('keyword', "");
        \View::share('types', $this->type);
        $type = "hot";

        $store_service = new StoreService();
        $list = $store_service->getHotStoreList(0, $keyword, $user);

        return view('Api.store.list', compact('list', 'keyword', 'type'));
    }

    public function getRecommendedStore(Request $request){
        try{
            $user = $this->userService->checkUserLogin();
        }catch (\Exception $e){
            return $e->getMessage();
        }
        $keyword = $request->get('keyword', "");
        \View::share('types', $this->type);
        $type = "recommended";

        $store_service = new StoreService();
        $list = $store_service->getRecommendedStoreList(0, $keyword,$user);

        return view('Api.store.list', compact('list', 'keyword', 'type'));
    }

    public function getStore(Request $request){
        try{
            $user = $this->userService->checkUserLogin();
        }catch (\Exception $e){
            return $e->getMessage();
        }
        $id = $request->get('id', 0);

        $field = sprintf('get_distance(lat,lng,%s,%s) as distance', $user['lat'], $user['lng']);
        $data = DB::table('store')
            ->where('id', $id)
            ->selectRaw($field)
            ->addSelect('store.*')
            ->first();

        $store_service = new StoreService();
        $check_business_time = $store_service->checkStoreAbnormalStatus($data);

        $data['abnormal_status'] = $check_business_time['status'];
        $data['tip_business_time'] = $check_business_time['str'];
        $data['delivery_fee'] = intval(calculate_delivery_fee($data['distance']));

        return view('Api.store.store', compact('data'));
    }

    public function getStoreFood(Request $request){
        $store_id = $request->get('store_id');

        $foods = DB::table('food')
            ->where('status', 1)
            ->where('store_id', $store_id)
            ->get();

        return $this->apiSuccess($foods);
    }

    public function getMyAddress(Request $request){
        try{
            $user = $this->userService->checkUserLogin();
        }catch (\Exception $e){
            return $e->getMessage();
        }

        $address_list = DB::table('user_address')
            ->where('user_id', $user['id'])
            ->get();

        return view('Api.user.address_list', compact('address_list'));
    }

    // 用户添加地址
    public function getAddressForm(Request $request){

        $id = $request->get('id', 0);
        $data = DB::table('user_address')->find($id);

        $data['next_city'] = 0;
        $data['next_next_city'] = 0;

        if ($id != 0){
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
        $data['city'] = DB::table('city')->where('parent_id', 0)->get()->toArray();

        return view('Api.user.address_form', compact('user', 'data'));
    }

    public function postAddressForm(Request $request){
        try{
            $user = $this->userService->checkUserLogin();
        }catch (\Exception $e){
            return $e->getMessage();
        }

        $data = $request->all();
        $data['user_id'] = $user['id'];

        $user_address_model = new UserAddressModel();
        if ($err = $user_address_model->validator($data)->first()){
            return $this->apiFail("", $err);
        }

        $id = $data['id'];
        unset($data['id']);

        if ($id != 0){
            $op = "修改";
            $success = DB::table('user_address')->where('id', $id)->update($data);
        }else{
            $op = "新增";
            $success = DB::table('user_address')->insertGetId($data);
            if ($user['default_address_id'] == 0){
                DB::table('user')
                    ->where('id', $user['id'])
                    ->update([
                       "default_address_id" => $success,
                       "update_time" => time()
                    ]);
                // 重置session中的用户信息
                $new_user = DB::table('user')
                    ->leftJoin('user_address as ua', 'ua.user_id', '=', 'user.id')
                    ->where('user.id', $user['id'])
                    ->select(['user.*', 'ua.lat', 'ua.lng', 'ua.address'])
                    ->first();
                \Session::put('user', $new_user);
            }
        }

        if ($success){
            return $this->apiSuccess("", "{$op}成功",url('/UserApi/myAddress'));
        }
        return $this->apiFail("", "{$op}失败");

    }

    public function deleteAddress(Request $request){
        try{
            $user = $this->userService->checkUserLogin();
        }catch (\Exception $e){
            return $e->getMessage();
        }

        $id = $request->get('id', 0);
        $address = DB::table('user_address')
            ->where('id', $id)
            ->where('user_id', $user['id'])
            ->first();

        if (!$address){
            return "获取地址信息失败";
        }

        DB::table('user_address')->where('id', $id)->delete();

        $address_list = DB::table('user_address')
            ->where('user_id', $user['id'])
            ->get();

        return view('Api.user.address_list', compact('address_list'));
    }

    public function pushMessage(Request $request){
        try{
            $user = $this->userService->checkUserLogin();
        }catch (\Exception $e){
            return $e->getMessage();
        }

        $message_list = DB::table('push')
            ->where('user_id', $user['id'])
            ->get();

        return view('Api.user.message_list', compact('message_list'));
    }

    public function deleteMessage(Request $request){
        try{
            $user = $this->userService->checkUserLogin();
        }catch (\Exception $e){
            return $e->getMessage();
        }
        $id = $request->get('id', 0);
        DB::table('push')
            ->where('user_id', $user['id'])
            ->where('id', $id)
            ->delete();

        $message_list = DB::table('push')
            ->where('user_id', $user['id'])
            ->get();

        return view('Api.user.message_list', compact('message_list'));
    }

    // 退出登录
    public function logout(){
        $user = \Session::get("user", null);

        $session_id_key = sprintf("user_%s", $user['id']);
        \Session::forget("user");
        \Cache::forget($session_id_key);

        return $this->apiSuccess("", "退出登录成功");
    }
}