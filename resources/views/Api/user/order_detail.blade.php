@extends('Api.base')

@section('style')
    <style>
        .container{
            width: 500px;
            min-height: 900px;
            overflow-y: auto;
            border: 1px solid black;
        }
        a :hover{
            text-decoration: none;
        }
    </style>
    @stop

@section('body')
    <div class="container" style="position: relative">
        <div class="head-background">
            <a href="javascript:history.go(-1)">
                <div style="position: absolute; height:40px; width:40px; border: 1px solid black; border-radius: 50%; text-align: center; line-height: 40px; font-size: 20px">
                    <span class="glyphicon glyphicon-arrow-left"></span>
                </div>
            </a>
            <a href="{{url('/UserApi/homePage')}}">
                <div style="position: absolute; right: 10px; font-size: 20px">
                    <span class="">首页</span>
                </div>
            </a>
            <span style="display: inline-block; height: 40px; line-height: 40px; font-size: 24px; text-align: center; width: 100%">订单详情</span>

        </div>

        <div class="order_status">
            <h2 style="padding-left: 20px">{{$order['order_status']}} ></h2>
            <hr>
        </div>

        <div class="order_detail" style="padding: 10px">
            <a href="{{url('UserApi/store')}}?id={{$order['store_id']}}">
                <div class="row">
                    <div class="col-md-2">
                        <img src="{{$order['avatar']}}" height="60px" width="60px">
                    </div>
                    <div class="col-md-8">
                        <span style="line-height: 60px; font-size: 18px">{{$order['name']}}</span>
                    </div>
                    <div class="col-md-2">
                        <span style="line-height: 60px; font-size: 18px"> > </span>
                    </div>
                </div>
            </a>
            <hr>

            @foreach($order['items'] as $k => $v)
                <div class="row" style="height: 30px;font-size: 16px">
                    <div class="col-md-7">{{$v['name']}}</div>
                    <div class="col-md-2">x{{$v['count_number']}}</div>
                    <div class="col-md-2">￥{{$v['count_number'] * $v['price']}}</div>
                </div>
                <hr />
                @endforeach

            <div class="row" style="height: 30px;font-size: 16px">
                <div class="col-md-9">配送费</div>
                <div class="col-md-2">￥{{$order['delivery_fee']}}</div>
            </div>
            <hr />

            <div class="row">
                <div class="col-md-7">
                    <a class="copyBtn" data-clipboard-text="{{$order['phone']}}" href="javascript:void(0)">
                        <span class="glyphicon glyphicon-earphone" ></span> 联系店家
                    </a>
                </div>
                <div class="col-md-5">实付 <span style="font-size: 18px">￥{{$order['actual_payment']}}</span></div>
            </div>

            <div style="margin-top: 25px">
                <div style="line-height: 20px">
                    <span style="display: inline-block; line-height: 20px; font-size: 16px; font-weight: bold">配送信息</span>
                    <hr />
                    <div class="row" style="font-size: 14px">
                        <div class="col-md-3">
                            送达时间:
                        </div>
                        <div class="col-md-8">
                            <span>尽快送达</span>
                        </div>
                    </div>
                    <hr />
                    <div class="row" style="font-size: 14px">
                        <div class="col-md-3">
                            送货地址:
                        </div>
                        <div class="col-md-8">
                            <span>{{$order['address_json']['linkman']}}</span><br />
                            <span>{{$order['address_json']['phone']}}</span><br />
                            <span>{{$order['address_json']['address']}}</span><br />
                            <span>地址备注 : {{$order['address_json']['remark']}}</span><br />
                        </div>
                    </div>
                    <hr />
                    <div class="row" style="font-size: 14px">
                        <div class="col-md-3">
                            配送方式:
                        </div>
                        <div class="col-md-8">
                            <span>官方配送</span>
                        </div>
                    </div>
                </div>
            </div>

            <div style="margin-top: 25px">
                <span style="display: inline-block; line-height: 20px; font-size: 16px; font-weight: bold">订单信息</span>
                <hr />
                <div class="row" style="font-size: 14px">
                    <div class="col-md-3">
                        支付方式:
                    </div>
                    <div class="col-md-8">
                        <span>{{$order['pay_type']}}</span>
                    </div>
                </div>
                <hr />
                <div class="row" style="font-size: 14px">
                    <div class="col-md-3">
                        下单时间:
                    </div>
                    <div class="col-md-8">
                        <span>{{date("Y-m-d H:i", $order['create_time'])}}</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
    @stop
@section('script')
    <script src="{{ asset('js/clipboard.min.js') }}"></script>
    <script>
        $(function () {
            var clipboard = new ClipboardJS('.copyBtn')
            clipboard.on('success', function (e) {
             layer.msg("复制店家号码成功")
            e.clearSelection();//清除选中样式（蓝色）
        })
             clipboard.on('error', function (e) {
                  console.error('Action:', e.action);
                   console.error('Trigger:', e.trigger);
             });
        })
    </script>
    @stop