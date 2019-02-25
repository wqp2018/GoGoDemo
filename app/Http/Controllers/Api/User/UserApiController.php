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
        try{
            $user = $this->userService->checkUserLogin();
        }catch (\Exception $e){
            return $e->getMessage();
        }

        return view('Api.user.index');
    }

    // 热门餐厅
    public function hotStore(){

    }
}