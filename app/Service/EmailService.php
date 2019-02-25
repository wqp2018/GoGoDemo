<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/22
 * Time: 15:47
 */

namespace App\Service;

use Mail;

class EmailService{

    public function sendEmail($email, $msg = ""){
        Mail::raw($msg, function ($message) use ($email){
            $message->subject("GoGo外卖平台注册");
            $message->to($email);
        });
    }

    // 验证邮箱格式
    public function checkEmailFormat($email){
        $regex= '/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/';

        // 格式正确， result 为 1，错误返回false
        $result = preg_match($regex,$email);

        return $result;
    }
}