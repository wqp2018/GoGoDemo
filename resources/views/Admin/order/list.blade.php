@extends('Admin.public.base')

@section('style')
    <style>
        body {
            overflow: scroll;
        }
        td {
            height: 48px;
            line-height: 48px !important;
        }
    </style>
@stop
@section('head')
    <link rel="stylesheet" href="{{asset('/css/bootstrap-datetimepicker.min.css')}}">
    <script type="text/javascript" src="{{ asset('/js/bootstrap-datetimepicker.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/js/bootstrap-datetimepicker.zh-CN.js') }}"></script>
@stop

@section('body')
    <div>
        <h3>订单列表</h3>
        <div class="item">
            <form id="form" action="{{url('/Order/list')}}">
                <div class="header">
                    <div class="col-lg-6">
                        <input type="checkbox" class="change_data" value="1" @if($cancel_order == 1) checked @endif name="cancel_order"> 已取消订单 &nbsp;
                        <input type="checkbox" class="change_data" value="1" @if($complete_order == 1) checked @endif name="complete_order"> 已完成订单 &nbsp;&nbsp;
                        <input type="text" style="display: inline-block;width: 20%;" class="input-sm form-datetime change_data" readonly name="select_time[begin_time]"> --
                        <input type="text" style="display: inline-block;width: 20%;" class="input-sm form-datetime change_data" readonly name="select_time[end_time]">
                    </div>
                    <div class="col-lg-6">
                            <div class="input-group">
                                <input type="text" name="keyword" value="{{$keyword or ''}}" class="form-control" placeholder="请输入用户姓名或店家名称">
                                <span class="input-group-btn">
                                    <button class="btn btn-default" onclick="order_search()" type="submit">查找</button>
                                </span>
                            </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="item">
            <table class="table">
                <thead>
                <tr>
                    <th>id</th>
                    <th>店家名称</th>
                    <th>订单状态</th>
                    <th>骑手信息</th>
                    <th>骑手联系方式</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                @forelse($list['data'] as $v)
                    <tr>
                        <td>{{$v['id']}}</td>
                        <td>{{$v['store_name']}}</td>
                        <td>{{$v['order_status_ch']}}</td>
                        <td>{{$v['driver_name'] or ""}}</td>
                        <td>{{$v['driver_phone'] or ""}}</td>
                        <td>
                            @if($v['order_status'] == 0)
                            <a href="javascript:void(0)" onclick="acceptOrder({{$v['id']}})">接单</a>
                                @endif
                            @if(in_array($v['order_status'], [0,1]))
                            <a href="javascript:void(0)" onclick="refuseOrder({{$v['id']}})">拒绝订单</a>
                                @endif
                            <a href="{{url('Order/orderDetail')."?id=".$v['id']}}">订单详情</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center;font-size: 18px">没有查找到数据！</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
            <span style="font-size: 18px">总共有 {{$list['total']}} 条结果，当前为第 {{$list['currentPage']}} 页</span>
            <div style="text-align: center">
                <ul class="pagination">
                    @if($list['currentPage'] == 1)
                        <li class="disabled"><span>&laquo;</span></li>
                        @else
                        <li><a href="javascript:void(0)" onclick="change_page({{$list['currentPage'] - 1}})">&laquo;</a></li>
                    @endif
                    @for($i = 1; $i <= $list['total_page']; $i++)
                            @if($list['currentPage'] == $i)
                                <li class="active"><span>{{$i}}</span></li>
                                @else
                                <li><a href="javascript:void(0)" onclick="change_page({{$i}})">{{$i}}</a></li>
                            @endif
                        @endfor
                        @if($list['currentPage'] == $list['total'])
                            <li class="disabled"><span>&raquo;</span></li>
                        @else
                            <li><a href="javascript:void(0)" rel="next" onclick="change_page({{$list['currentPage'] + 1}})">&raquo;</a></li>
                        @endif
                </ul>
            </div>
        </div>
    </div>
@stop
@section('script')
    <script>
        $(function () {
            $('.form-datetime').datetimepicker({
                format: 'yyyy-mm-dd',
                language: "zh-CN",
                startView: "month",
                minView: "month",
                autoclose: true
            });
            $("input[name='select_time[begin_time]']").val("{{$select_time['begin_time'] or date('Y-m-d')}}")
            $("input[name='select_time[end_time]").val("{{$select_time['end_time'] or date('Y-m-d')}}")

            $(".change_data").change(function () {
                $("#form").submit();
            })
        })

        function order_search() {
            $("#form").submit();
        }

        function change_page(index) {
            var data = $("#form").serialize() + "&page=" + index;

            window.location.href = "{{url('Order/list')}}?" + data;
        }
        
        function acceptOrder(order_id) {
            var url = "{{url('Order/acceptOrder')}}?order_id=" + order_id;
            $.ajax({
                url: url,
                type: "post",
                success: function (res) {
                    layer.msg(res.message)
                    setTimeout(function () {
                        window.location.reload()
                    }, 1200)
                }
            })

        }

        function refuseOrder(order_id) {
            var url = "{{url('Order/refuseOrder')}}?order_id=" + order_id;
            $.ajax({
                url: url,
                type: "post",
                success: function (res) {
                    layer.msg(res.message)
                    setTimeout(function () {
                        window.location.reload()
                    }, 1200)
                }
            })
        }
    </script>
    @stop