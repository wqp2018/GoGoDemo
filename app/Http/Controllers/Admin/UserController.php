<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Database\Query\Builder;
use DB;
use Illuminate\Http\Request;

class UserController extends BaseController
{
    //
    public function getList(Request $request){
        $this->markPage();
        $keyword = $request->get('keyword');

        $builder = DB::table('user');
        if ($keyword){
            $like_str = sprintf("%%%s%%",$keyword);
            $builder->where(function ($query) use ($like_str){
                $query->where('user_name','like',$like_str)
                    ->orWhere('phone','like',$like_str);
            });
        }
        $list = $builder->paginate(env('PAGE_SIZE',10));

        return view('user.list',compact('list','keyword'));
    }

    public function postStatus(Request $request)
    {
        return parent::postStatus($request);
    }
}
