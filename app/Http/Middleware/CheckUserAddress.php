<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/4
 * Time: 2:10
 */

namespace App\Http\Middleware;

use Closure;

class CheckUserAddress
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // 用户未登录，返回用户登录页面
        $user = \Session::get('user', null);
        if ($user == null){
            return redirect('login');
        }
        // 检查用户是否填写了地址
        if ($user['default_address_id'] == 0){
            return redirect('UserApi/addressForm');
        }
        return $next($request);
    }
}
