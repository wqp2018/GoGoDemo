<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/22
 * Time: 4:28
 */

namespace App\Service;

use DB;

class UserService{

    // 检查用户是否已经登录
    public function checkUserLogin(){
        $user = \Session::get("user", null);

        if ($user == null){
            throw new \Exception("请登录");
        }

        $session_id_key = sprintf("user_%s", $user['id']);
        // 获取缓存中的key
        $session_id = \Session::get($session_id_key);
        // 获取redis中的key
        $redis_session_key = \Cache::get($session_id_key);
        if ($session_id != $redis_session_key){
            \Session::forget('user');
            throw new \Exception("登录信息已失效，请重新登录");
        }

        return $user;
    }

    // 检查用户是否存在一个默认地址
    public function checkUserAddress($user_id){
        $result = DB::table('user')->find($user_id);

        if ($result['default_address_id'] == 0){
            return false;
        }

        return true;
    }
}