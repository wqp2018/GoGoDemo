<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/28
 * Time: 18:20
 */

namespace App\Model;

class FoodModel extends BaseModel{
    protected $table = "food";

    public $timestamps = false;

    protected $fillable = ["name", "store_id", "avatar", "price", "inventory", "total_sale", "status",
        "create_time", "update_time"];

    protected $rule = [
        "name" => "required",
        "store_id" => "required|integer|min:1",
        "inventory" => "required|integer|min:0",
        "avatar" => "required"
    ];

    protected $messages = [
        "required" => ":attribute 不能为空",
        "store_id.min" => ":attribute 选择有误",
        "inventory.min" => ":attribute 不能为负数",
    ];

    protected $attributes = [
        "name" => "餐点名称",
        "store_id" => "店家",
        "inventory" => "库存",
        "avatar" => "餐点图片"
    ];
}