<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/1
 * Time: 4:19
 */

namespace App\Service;

use DB;

class PushService{

    public function pushUserMessage($user_id = 0, $msg = ""){
        DB::table('push')
            ->insert([
                "user_id" => $user_id,
                "message" => $msg,
                "create_time" => time()
            ]);
    }

}