<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/22
 * Time: 4:28
 */

namespace App\Service;


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
            throw new \Exception("登录信息已失效，请重新登录");
        }

        return $user;
    }
}