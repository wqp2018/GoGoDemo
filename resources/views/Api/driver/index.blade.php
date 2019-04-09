@extends('Api.base')

@section('style')
    <style>
        .container{
            width: 500px;
            height: 900px;
            overflow-y: auto;
        }
        .order_div{
            background-color: #F8F5F4;
            padding: 5px;
            height: 150px;
            margin-top: 15px;
        }
    </style>
@stop

@section('body')
    <div class="container">
        <div class="food-list" style="height: 600px;">
            <ul style="text-align: center" id="myTab" class="nav nav-tabs">
                <li class="active" style="width: 33%"><a href="#no" data-toggle="tab">待接订单</a></li>
                <li style="width: 33%"><a href="#have" data-toggle="tab">已接订单</a></li>
                <li style="width: 33%"><a href="#refuse" data-toggle="tab">已取消订单</a></li>
            </ul>
            <div id="myTabContent" class="tab-content">
                <div class="tab-pane fade in active" id="no">
                    <div class="list-group">
                        @foreach($order_list as $k => $v)
                            <div class="list-group-item">
                                <span>订单编号：{{$v['id']}}</span><br />
                                <span>店家名称：{{$v['store_name']}}</span><br />
                                <span>店家地址：{{$v['store_address_json']}}</span><br />
                                <span>收货用户：{{json_decode($v['address_json'], true)['linkman']}}，联系电话：{{json_decode($v['address_json'], true)['phone']}}</span><br />
                                <span>收货地址：{{json_decode($v['address_json'], true)['address']}}</span><br />
                                <div class="row">
                                    <div class="col-md-3">食物列表：</div>
                                    <div class="col-md-5">
                                        @foreach($v['food'] as $key => $val)
                                            <span>{{$val['name']}}</span>&nbsp;&nbsp;<span>X{{$val['count_number']}}</span><br>
                                            @endforeach
                                    </div>
                                    <div class="col-md-4">
                                        <button class="btn btn-primary" onclick="driverAcceptOrder({{$v['id']}})">接单</button>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                    </div>
                </div>
                <div class="tab-pane fade" style="padding-left: 10px" id="have">
                    <div class="list-group">
                        @foreach($have_order as $k => $v)
                            <div class="list-group-item">
                                <span>订单编号：{{$v['id']}}</span>&nbsp;&nbsp; 订单状态：<span style="color: red">{{$order_status[$v['order_status']]}}</span><br />
                                <span>店家名称：{{$v['store_name']}}</span><br />
                                <span>店家地址：{{$v['store_address_json']}}</span><br />
                                <span>收货用户：{{json_decode($v['address_json'], true)['linkman']}}，联系电话：{{json_decode($v['address_json'], true)['phone']}}</span><br />
                                <span>收货地址：{{json_decode($v['address_json'], true)['address']}}</span><br />
                                <div class="row">
                                    <div class="col-md-3">食物列表：</div>
                                    <div class="col-md-5">
                                        @foreach($v['food'] as $key => $val)
                                            <span>{{$val['name']}}</span>&nbsp;&nbsp;<span>X{{$val['count_number']}}</span><br>
                                        @endforeach
                                    </div>
                                    <div class="col-md-4">
                                        @if($v['order_status'] == 2)
                                        <button class="btn btn-primary" onclick="driverRefuseOrder({{$v['id']}})">取消接单</button>
                                        @endif
                                        @if($v['order_status'] == 4)
                                                <button class="btn btn-default" disabled="disabled">订单已完成</button>
                                            @else
                                                <button class="btn btn-primary" onclick="driverFinishOrder({{$v['id']}})">完成订单</button>
                                            @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="tab-pane fade" style="padding-left: 10px" id="refuse">
                    <div class="list-group">
                        @foreach($refuse_order as $k => $v)
                            <div class="list-group-item">
                                <span>订单编号：{{$v['id']}}</span><br />
                                <span>店家名称：{{$v['store_name']}}</span><br />
                                <span>店家地址：{{$v['store_address_json']}}</span><br />
                                <span>收货用户：{{json_decode($v['address_json'], true)['linkman']}}，联系电话：{{json_decode($v['address_json'], true)['phone']}}</span><br />
                                <span>收货地址：{{json_decode($v['address_json'], true)['address']}}</span><br />
                                <div class="row">
                                    <div class="col-md-3">食物列表：</div>
                                    <div class="col-md-5">
                                        @foreach($v['food'] as $key => $val)
                                            <span>{{$val['name']}}</span>&nbsp;&nbsp;<span>X{{$val['count_number']}}</span><br>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </div>
@stop

@section('script')
    <script>
        $(function () {

        })
        var page = 0;

        function driverAcceptOrder(order_id) {
            var url = "{{url('/DriverApi/acceptOrder')}}?order_id=" + order_id;
            $.ajax({
                url: url,
                success: function (res) {
                    if (res.status == 0){
                        layer.msg(res.msg)
                    } else {
                        layer.msg(res.msg)
                        window.location.reload()
                    }
                }
            })
        }

        function driverRefuseOrder(order_id) {
            var url = "{{url('/DriverApi/refuseOrder')}}?order_id=" + order_id;
            $.ajax({
                url: url,
                success: function (res) {
                    if (res.status == 0){
                        layer.msg(res.msg)
                    } else {
                        layer.msg(res.msg)
                        window.location.reload()
                    }
                }
            })
        }

        function driverFinishOrder(order_id) {
            var url = "{{url('/DriverApi/finishOrder')}}?order_id=" + order_id;
            $.ajax({
                url: url,
                success: function (res) {
                    if (res.status == 0){
                        layer.msg(res.msg)
                    } else {
                        layer.msg(res.msg)
                        window.location.reload()
                    }
                }
            })
        }
    </script>
@stop