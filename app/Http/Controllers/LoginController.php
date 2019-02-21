<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/21
 * Time: 16:00
 */

namespace App\Http\Controllers;

use Gregwar\Captcha\CaptchaBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Session;
use DB;

class LoginController extends Controller{

    public function userLogin(){
        return view('login');
    }

    public function postUserLogin(){

    }

    public function adminLogin(){
        $admin = 1;
        return view('login', compact('admin'));
    }

    // 管理员登录
    public function postAdminLogin(Request $request){
        $data = $request->all();
        $captcha = Session::get('myCaptcha');

        $response = [
            "code" => 0,
            "msg" => ""
        ];

        if (!$data['captcha'] || strcasecmp($captcha, $data['captcha']) != 0){
            $response['msg'] = "验证码错误，请重新输入";
            return $response;
        }

        $adminUser = DB::table('admin_user')
            ->where('user_name', $data['username'])
            ->first();

        if ($adminUser){
            $password = Crypt::decrypt($adminUser['password_encry']);
            if ($password == $data['password']){
                Session::put('admin', $adminUser);

                $response['code'] = 1;
                $response['msg'] = "登录成功，即将跳转";
                return $response;
            }
        }

        $response['msg'] = "用户名或密码错误";
        return $response;

    }

    // 生成验证码
    public function getCaptcha(){
        $builder = new CaptchaBuilder();

        $builder->build($width = 200, $height = 40, $font = null);

        //获取验证码的内容
        $phrase = $builder->getPhrase();

        //把内容存入session
        Session::put('myCaptcha', $phrase);
        //生成图片
        header("Cache-Control: no-cache, must-revalidate");
        header('Content-Type: image/jpeg');
        $builder->output();
    }

}