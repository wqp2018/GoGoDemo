<?php

namespace App\Http\Middleware;

use Closure;

class CheckAdminLogin
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
        // 管理员未登录，返回用户登录页面
        $admin = \Session::get('admin', null);
        if ($admin == null){
            return redirect('login');
        }
        return $next($request);
    }
}
