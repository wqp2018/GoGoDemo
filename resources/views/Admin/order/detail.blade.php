@extends('Admin.public.base')

@section('style')
    <style>
        .form-item{
            margin-bottom: 20px;
        }
    </style>
@stop
@section('head')
    <link rel="stylesheet" href="{{asset('/css/bootstrap-datetimepicker.min.css')}}">
    <script type="text/javascript" src="{{ asset('/js/bootstrap-datetimepicker.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/js/bootstrap-datetimepicker.zh-CN.js') }}"></script>
@stop
@section('body')
        <h3 class="text-primary">订单详情</h3>
        <span style="color: red">{{$order_status[$data['order_status']]}}</span>

        <div style="margin-top: 40px">
            <div class="form-item input-group input-group-lg">
                <span class="input-group-addon" id="sizing-food-name">店家名称</span>
                <input type="text" readonly value="{{$data['store_name'] or ""}}" class="form-control" aria-describedby="sizing-food-name">
            </div>

            <div class="form-item input-group input-group-lg">
                <span class="input-group-addon" id="sizing-food-name">店家联系电话</span>
                <input type="text" readonly value="{{$data['store_phone'] or ""}}" class="form-control" aria-describedby="sizing-food-name">
            </div>

            <div class="form-item input-group input-group-lg">
                <span class="input-group-addon" id="sizing-food-name">店家地址</span>
                <input type="text" readonly value="{{$data['store_address_json'] or ""}}" class="form-control" aria-describedby="sizing-food-name">
            </div>

            <div class="form-item input-group input-group-lg">
                <span class="input-group-addon" id="sizing-food-name">收货人</span>
                <input type="text" readonly value="{{json_decode($data['address_json'], true)['linkman']}}" class="form-control" aria-describedby="sizing-food-name">
            </div>

            <div class="form-item input-group input-group-lg">
                <span class="input-group-addon" id="sizing-food-name">收货人联系方式</span>
                <input type="text" readonly value="{{json_decode($data['address_json'], true)['phone']}}" class="form-control" aria-describedby="sizing-food-name">
            </div>

            <div class="form-item input-group input-group-lg">
                <span class="input-group-addon" id="sizing-food-name">用户地址备注</span>
                <input type="text" readonly value="{{json_decode($data['address_json'], true)['remark']}}" class="form-control" aria-describedby="sizing-food-name">
            </div>

            <table class="table">
                <thead>
                <tr>
                    <th>食物名称</th>
                    <th>价格</th>
                    <th>购买数量</th>
                </tr>
                </thead>
                <tbody>
                @foreach($data['food'] as $k => $v)
                    <tr>
                        <td>{{$v['name']}}</td>
                        <td>{{$v['price']}}</td>
                        <td>{{$v['count_number']}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="form-item input-group input-group-lg">
                <span class="input-group-addon" id="sizing-food-name">订单备注</span>
                <input type="text" readonly value="{{$data['remark'] or ""}}" class="form-control" aria-describedby="sizing-food-name">
            </div>

            <div class="form-group center-block">
                <a href="javascript:history.go(-1)" class="btn btn-default">返回</a>
            </div>
        </div>

@stop
@section('script')
    <script>
        $(function () {
        })
    </script>
@stop