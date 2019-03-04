<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/27
 * Time: 20:57
 */

namespace App\Model;

class StoreModel extends BaseModel{
    protected $table = "store";

    public $timestamps = false;

    protected $fillable = ["name", "phone", "avatar", "lat", "lng", "address", "status", "pay_type", "city_id",
        "delivery_range", "business_time", "total_sale", "create_time", "update_time"];

    protected $rule = [
        "name" => "required",
        "phone" => "required",
        "lat" => "required",
        "lng" => "required",
        "city_id" => "required|integer|min:1",
        "delivery_range" => "required|integer|min:0",
    ];

    protected $messages = [
        "required" => ":attribute 不能为空",
        "delivery_range.min" => ":attribute 不能小于0",
        "city_id.min" => ":attribute 选择有误",
    ];

    protected $attributes = [
        "name" => "商店名",
        "phone" => "联系电话",
        "lat" => "纬度",
        "lng" => "经度",
        "delivery_range" => "配送范围",
        "city_id" => "城市",
    ];
}