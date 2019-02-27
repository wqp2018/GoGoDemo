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
//        if (Session::get('user') != null){
//            return redirect('');
//        }
        return view('login');
    }

    public function postUserLogin(Request $request){
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

        $user = DB::table('user')
            ->where('user_name', $data['username'])
            ->first();

        if ($user){
            $password = Crypt::decrypt($user['password_encry']);
            if ($password == $data['password']){
                $this->userDoLogin($user);

                $response['code'] = 1;
                $response['msg'] = "登录成功，即将跳转";
                $response['url'] = url('UserApi/homePage');
                return $response;
            }
        }

        $response['msg'] = "用户名或密码错误";
        return $response;

    }

    // 用户登录
    private function userDoLogin($user){
        $session_id = Session::getId();

        // 本地缓存用户信息
        Session::put("user", $user);
        // user_id 保存用户的session_id
        $session_user_id = sprintf("user_%s", $user['id']);
        // 缓存到本地，验证需要
        Session::put($session_user_id, $session_id);

        // 先获取redis 中的sessionId
        $redis_session_id = \Cache::get($session_user_id);
        // 两个session_id 不同，则说明session 过期， 或者异处登录
        if ($redis_session_id != $session_id){
            \Cache::put($session_user_id, $session_id, env("CACHE_MINUTES", 30));
        }
    }

    public function adminLogin(){
        $admin = 1;
        // 若已登录，直接进入系统
        if (Session::get('admin') != null){
            return redirect('User/list');
        }
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
                $response['url'] = url("User/list");
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