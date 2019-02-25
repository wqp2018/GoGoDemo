<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/22
 * Time: 16:31
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class TestApiController extends Controller{

    public function getTest(){
        return \Crypt::encrypt(12345678);
    }
}