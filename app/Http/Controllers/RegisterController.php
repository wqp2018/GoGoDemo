<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/22
 * Time: 14:40
 */

namespace App\Http\Controllers;

use App\Model\UserModel;
use App\Service\EmailService;
use Illuminate\Http\Request;
use DB;

class RegisterController extends Controller{

    public function ajaxSuccess($message = '',$url = ""){
        $return['code'] = 1;
        $return['msg'] = $message;
        $return['url'] = $url;

        return $return;
    }

    public function ajaxFail($message = ''){
        $return['code'] = 0;
        $return['msg'] = $message;

        return $return;
    }

    // 获取注册页面
    public function getRegister(){
        return view('register');
    }

    // 注册
    public function postRegister(Request $request, UserModel $userModel){
        $data = $request->all();

        if ($err = $userModel->validator($data)->first()){
            return $this->ajaxFail($err);
        }

        if ($data['validate_code'] == ""){
            return $this->ajaxFail("请先输入验证码");
        }

        // 获取验证码
        $validate_code_key = sprintf("email_%s", $data['email']);
        $validate_code = \Cache::get($validate_code_key);
        if ($validate_code != $data['validate_code']){
            return $this->ajaxFail("验证码错误，请检查后重新输入");
        }

        unset($data['validate_code']);
        $data['password_encry'] = \Crypt::encrypt($data['password_encry']);
        $data['create_time'] = time();
        $success = DB::table('user')->insert($data);

        if ($success){
            return $this->ajaxSuccess("注册成功", url('/login'));
        }
        return $this->ajaxFail("注册失败");
    }

    // 发送邮件
    public function sendEmail(Request $request){
        $data = $request->all();
        $response = [
            "code" => 0,
            "msg" => ""
        ];

        $emailService = new EmailService();

        $email_format = $emailService->checkEmailFormat($data['email']);
        if ($email_format == false){
            $response['msg'] = "邮箱格式错误，请重新输入";
            return $response;
        }

        // 获取六位注册验证码
        $validate_code = getRandNum(6);
        $validate_cache_key = sprintf("email_%s", $data['email']);
        \Cache::put($validate_cache_key, $validate_code, 5);

        // 发送的信息
        $msg = "【GoGo外卖平台】验证码: {$validate_code}(有效期 5 分钟).您正在使用邮箱验证码注册功能，该验证码仅用于身份验证，请勿泄露给他人使用";

        try{
            $emailService->sendEmail($data['email'], $msg);
        }catch (\Exception $e){
            $response['msg'] = $e->getMessage();
            return $response;
        }

        $response['code'] = 1;
        $response['msg'] = "发送邮件成功";

        return $response;
    }

}