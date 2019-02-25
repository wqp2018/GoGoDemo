<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/22
 * Time: 3:40
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class BaseApiController extends Controller{

    public function apiSuccess($data = [], $msg = ""){
        $return['status'] = 1;
        $return['data'] = $data;
        $return['msg'] = $msg;

        return $return;
    }

    public function apiFail($data = [], $msg = ""){
        $return['status'] = 0;
        $return['data'] = $data;
        $return['msg'] = $msg;

        return $return;
    }
}