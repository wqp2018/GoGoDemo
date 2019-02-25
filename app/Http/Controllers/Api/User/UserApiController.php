<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/22
 * Time: 3:39
 */

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Api\BaseApiController;
use App\Service\UserService;

class UserApiController extends BaseApiController {

    protected $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }

    // 用户首页
    public function getHomePage(){
        return \Session::get("user");
    }

    // 热门餐厅
    public function hotStore(){

    }
}