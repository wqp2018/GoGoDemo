<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/22
 * Time: 3:40
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;

class BaseApiController extends Controller{

    public function __construct()
    {

    }

    public function apiSuccess($data = [], $msg = "", $url = ""){
        $return['status'] = 1;
        $return['data'] = $data;
        $return['msg'] = $msg;
        $return['url'] = $url;

        return $return;
    }

    public function apiFail($data = [], $msg = ""){
        $return['status'] = 0;
        $return['data'] = $data;
        $return['msg'] = $msg;

        return $return;
    }
}