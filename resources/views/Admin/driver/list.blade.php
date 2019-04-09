@extends('Admin.public.base')

@section('body')
    <div>
        <h3>骑手列表</h3>
        <div class="item">
            <div class="header">
                <div class="col-lg-6">
                    <a class="btn btn-primary" href="{{url('Driver/form')}}">新增</a>
                </div>
                <div class="col-lg-6">
                    <form action="{{url('/Driver/list')}}">
                        <div class="input-group">
                            <input type="text" name="keyword" value="{{$keyword or ''}}" class="form-control" placeholder="请输入骑手名称">
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
                        <th>骑手名称</th>
                        <th>联系方式</th>
                        <th>完成订单量</th>
                        <th>取消订单数量</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($list as $v)
                        <tr>
                            <td><input class="select_id" name="ids" type="checkbox" value="{{$v['id']}}"></td>
                            <td>{{$v['id']}}</td>
                            <td>{{$v['name']}}</td>
                            <td>{{$v['phone']}}</td>
                            <td>{{$v['finish_count']}}</td>
                            <td>{{$v['refuse_count']}}</td>
                            <td>
                                <a href="{{action('Admin\FoodController@getForm', ['id' => $v['id']])}}"><span class="glyphicon glyphicon-pencil"></span></a>
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