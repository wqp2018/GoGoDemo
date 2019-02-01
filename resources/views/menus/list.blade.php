@extends('public.base')

@section('style')
    <style>
        td {
            height: 48px;
            line-height: 48px !important;
        }
    </style>
@stop

@section('body')
    <div>
        <h3>菜单列表</h3>
        <div class="item">
            <div class="header">
                <div class="col-lg-6">
                    <a class="btn btn-info" href="{{url('Menus/form')}}">新增</a>
                    <a class="btn btn-info" href="">删除</a>
                </div>
                <div class="col-lg-6">
                    <form action="{{url('/Menus/list')}}">
                        <div class="input-group">
                            <input type="text" name="keyword" value="{{$keyword or ''}}" class="form-control" placeholder="请输入菜单名称">
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="submit">查找</button>
                            </span>
                        </div>
                    </form>
                </div>
            </div>
            <div class="item">
                <table class="table">
                    <thead>
                    <tr>
                        <th><input class="ids" type="checkbox"></th>
                        <th>id</th>
                        <th>菜单名称</th>
                        <th>状态</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($list as $v)
                        <tr>
                            <td><input type="checkbox" value="{{$v['id']}}"></td>
                            <td>{{$v['id']}}</td>
                            <td>{{$v['name']}}</td>
                            <td>
                                @if($v['status'] == 1)
                                    <a class="ajax-post" url="{{action('Admin\UserController@postStatus',['status' => abs(1-$v['status']),'id' => $v['id'],'mod' => 'user'])}}"><span class="glyphicon glyphicon-ok"></span></a>
                                @else
                                    <a class="ajax-post" url="{{action('Admin\UserController@postStatus',['status' => abs(1-$v['status']),'id' => $v['id'],'mod' => 'user'])}}"><span class="glyphicon glyphicon-remove"></span></a>
                                @endif
                            </td>
                            <td>
                                <a href="{{action('Admin\MenusController@getForm', ['id' => $v['id']])}}">编辑</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center;font-size: 18px">没有查找到数据！</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
                <span style="font-size: 18px">总共有 {{$list->total()}} 条结果，当前为第 {{$list->currentPage()}} 页</span>
                <div style="text-align: center">
                    {{ $list->links() }}
                </div>

            </div>

        </div>
    </div>
@stop