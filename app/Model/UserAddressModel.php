<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/20
 * Time: 3:55
 */

namespace App\Model;

class UserAddressModel extends BaseModel{

    protected $table = "user_address";

    public $timestamps = false;

    protected $fillable = ["user_id", "city_id", "linkman", "phone", "address", "lat", "lng",
        "create_time", "update_time"];

    protected $rule = [
        "user_id" => "required",
        "city_id" => "required|integer|min:1",
        "phone" => "required",
        "address" => "required",
        "linkman" => "required",
        "lat" => "required",
        "lng" => "required",
    ];

    protected $messages = [
        "required" => ":attribute 不能为空",
        "city_id.min" => ":attribute 选择有误",
    ];

    protected $attributes = [
        "user_id" => "用户信息错误",
        "city_id" => "所在区域",
        "phone" => "联系方式",
        "address" => "详细地址",
        "linkman" => "联系人",
        "lat" => "经纬度",
        "lng" => "经纬度"
    ];
}