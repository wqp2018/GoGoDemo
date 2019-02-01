<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/20
 * Time: 3:55
 */

namespace App\Model;

class MenusModel extends BaseModel{

    protected $table = "menus";

    public $timestamps = false;

    protected $fillable = ["name", "parent_id", "url", "create_time", "update_time"];

    protected $rule = [
        "name" => "required"
    ];

    protected $messages = [
        "name.required" => ":attribute 不能为空"
    ];

    protected $attributes = [
      "name" => "菜单名称"
    ];

}