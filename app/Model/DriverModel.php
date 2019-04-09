<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/6
 * Time: 21:19
 */

namespace App\Model;

class DriverModel extends BaseModel{
    protected $table = "driver";

    public $timestamps = false;

    protected $fillable = ["name", "phone", "avatar", "pass_word", "finish_count", "refuse_count",
        "create_time", "update_time"];

    protected $rule = [
        "name" => "required",
        "phone" => "required",
        "pass_word" => "required"
    ];

    protected $messages = [
        "required" => ":attribute 不能为空",
    ];

    protected $attributes = [
        "name" => "骑手名称",
        "phone" => "手机号码",
        "pass_word" => "密码"
    ];
}