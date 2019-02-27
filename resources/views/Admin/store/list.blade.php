@extends('Admin.public.base')

@section('body')
    <div>
        <h3>店家列表</h3>
        <div class="item">
            <div class="header">
                <div class="col-lg-6">
                    <a class="btn btn-primary" href="{{url('Store/form')}}">新增</a>
                    <button type="button" url="{{action('Admin\StoreController@postStatus', ['mod' => 'store'])}}" class="btn btn-primary btn_enable">启用</button>
                    <button type="button" url="{{action('Admin\StoreController@postStatus', ['mod' => 'store'])}}" class="btn btn-primary btn_disable">禁用</button>
                </div>
                <div class="col-lg-6">
                    <form action="{{url('/User/list')}}">
                        <div class="input-group">
                            <input type="text" name="keyword" value="{{$keyword or ''}}" class="form-control" placeholder="请输入店家名称">
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
                        <th><input name="select_all" class="ids" type="checkbox"></th>
                        <th>id</th>
                        <th>店家名称</th>
                        <th>店家图片</th>
                        <th>联系方式</th>
                        <th>总销量</th>
                        <th>状态</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($list as $v)
                        <tr>
                            <td><input class="select_id" name="ids" type="checkbox" value="{{$v['id']}}"></td>
                            <td>{{$v['id']}}</td>
                            <td>{{$v['name']}}</td>
                            <td><img src="{{$v['avatar']}}" height="48px" width="48px"></td>
                            <td>{{$v['phone']}}</td>
                            <td>
                                @if($v['status'] == 1)
                                    <a class="ajax-post" url="{{action('Admin\StoreController@postStatus',['status' => abs(1-$v['status']),'id' => $v['id'],'mod' => 'store'])}}"><span class="glyphicon glyphicon-ok"></span></a>
                                @else
                                    <a class="ajax-post" url="{{action('Admin\StoreController@postStatus',['status' => abs(1-$v['status']),'id' => $v['id'],'mod' => 'store'])}}"><span class="glyphicon glyphicon-remove"></span></a>
                                @endif
                            </td>
                            <td>
                                <a href="{{action('Admin\UserController@getForm', ['id' => $v['id']])}}"><span class="glyphicon glyphicon-pencil"></span></a>
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