<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/20
 * Time: 3:55
 */

namespace App\Model;

class UserModel extends BaseModel{

    protected $table = "user";

    public $timestamps = false;

    protected $fillable = ["user_name", "phone", "password_encry", "avatar", "email", "recommended_id",
        "status", "gogo_coin", "create_time", "update_time"];

    protected $rule = [
        "user_name" => "required|unique:user",
        "password_encry" => "required|min:8",
        "email" => "required|email"
    ];

    protected $messages = [
        "user_name.required" => ":attribute 不能为空",
        "user_name.unique" => ":attribute 已被注册",
        "password_encry.required" => ":attribute 不能为空",
        "password_encry.min" => ":attribute 不能小于8位数",
        "email.required" => ":attribute 邮箱不能为空",
        "email.email" => ":attribute 邮箱格式错误"
    ];

    protected $attributes = [
        "user_name" => "用户名",
        "password_encry" => "密码",
        "email" => "邮箱"
    ];
}