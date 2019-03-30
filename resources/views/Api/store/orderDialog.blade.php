@extends('Api.base')

@section('style')
    <style>
        .container{
            width: 500px;
            min-height: 900px;
            border: 1px solid black;
        }
        .select_address{
            height: 80px;
            margin-top: 40px;
        }
        .show_address{
            display: inline-block;
            height: 80px;
            width: 80px;
        }
        .address_message{
            display: inline-block;
            position: relative;
        }
        .message_info{
            font-size: 20px;
        }
        .store_message{
            margin-top: 10px;
        }
    </style>
@stop

@section('body')
    <form id="ordering" action="{{url('OrderApi/ordering')}}" method="post">
        {{ csrf_field() }}
        <input name="store_id" value="{{$store['id']}}" hidden>
        <input name="select_food" value="{{json_encode($select_foods)}}" hidden>
        <div id="app" v-cloak class="container" style="position: relative">
            <div class="head-background">
                <a href="javascript:history.go(-1)">
                    <div style="position: absolute; height:40px; width:40px; border: 1px solid black; border-radius: 50%; text-align: center; line-height: 40px; font-size: 20px">
                        <span class="glyphicon glyphicon-arrow-left"></span>
                    </div>
                </a>
                <span style="display: inline-block; height: 40px; line-height: 40px; font-size: 24px; text-align: center; width: 100%">确认订单</span>
            </div>
            <div class="select_address row">
                <div class="show_address col-md-4">
                    <img src="/uploads/images/address.jpg" width="60px" height="80px">
                </div>
                <div class="address_message col-md-10">
                    <span class="message_info linkman">{{$address['linkman']}}</span>
                    <span class="message_info phone" style="display: inline-block; position: relative; left: 40%">{{$address['phone']}}</span><br>
                    <span class="message_info address">{{$address['address']}}</span><br>
                    <input name="address_id" value="{{$address['id']}}" hidden>
                    <span style="margin-right: 10px"><a href="javascript:void(0)" onclick="showAddress()">其他地址</a></span>
                </div>
            </div>
            <div style="margin-top: 10px; font-size: 18px">
                <div style="border-bottom: 1px #1E9FFF solid; padding: 15px">
                    <span>送达时间</span>
                    <span style="display: inline-block; position: relative; left: 45%; color: #1E9FFF">尽快送达({{$expect_delivery_time}})</span><br>
                </div>
                <div style="padding: 15px">
                    <span>支付方式</span>
                    <span style="display: inline-block; position: relative; left: 55%; color: #1E9FFF">到付</span><br>
                </div>
            </div>
            <div class="store_message" style="">
                <div>
                    <span style="font-size: 16px;font-weight: bold">{{$store['name']}}</span><br>
                    <span style="display: inline-block; width: 100%; text-align: center; border-bottom: 1px black solid"></span>
                </div>

                @foreach($select_foods as $k => $v)
                    <div class="row" style="">
                        <div class="col-md-2">
                            <img src="{{$v['avatar']}}" height="60px" width="60px">
                        </div>
                        <div class="col-md-5" style="padding: 5px">
                            <span style="font-size: 15px">{{$v['name']}}</span><br>
                            <span style="display: inline-block;width: 60%;color: gray;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;">{{$v['items']}}</span>
                        </div>
                        <div class="col-md-2" style="line-height: 60px">
                            x  {{$v['count']}}
                        </div>
                        <div class="col-md-2" style="line-height: 60px; color: red">
                            ￥{{$v['price'] * $v['count']}}
                        </div>
                    </div>
                    <span style="display: inline-block; height: 1px; width: 100%; text-align: center; border-bottom: 1px gray solid; opacity: 0.5"></span>
                        @endforeach
                <div class="row" style="height: 30px;">
                    <div class="col-md-9" style="padding: 15px">
                        <span style="background-color: #6D9BF5; color: white;display: inline-block; width: 36px; height: 20px; text-align: center;">商家</span>
                        <span style="font-size: 15px; margin-left: 8px">配送费({{round($store['distance'] / 1000, 2)}}km)</span>
                    </div>
                    <div class="col-md-2" style="padding-top: 15px">
                        ￥{{$delivery_fee}}
                    </div>
                </div>
                <span style="display: inline-block; height: 1px; width: 100%; text-align: center; border-bottom: 1px gray solid; opacity: 0.5"></span>

                <div class="row" style="height: 30px; padding-top: 10px">
                    <div class="col-md-6">
                        <span style="font-size: 15px; font-weight: bold">订单备注</span>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="口味、偏好" aria-describedby="basic-addon1">
                        </div>
                    </div>
                </div>
                <span style="margin-bottom: 60px; margin-top: 20px; display: inline-block; height: 1px; width: 100%; text-align: center; border-bottom: 1px gray solid; opacity: 0.5"></span>
            </div>

            <div class="row" style="position:absolute; bottom: 0px; height: 50px; width: 100%;">
                <div class="col-md-8" style="height: 50px; background-color: #545150; color: white; line-height: 50px">
                    <span style="font-size: 16px">
                        ￥<?php
                            $total_price = 0;
                            foreach ($select_foods as $k => $v){
                                $total_price += $v['count'] * $v['price'];
                            }
                            echo $total_price + $delivery_fee;
                        ?>
                    </span>
                </div>
                <div onclick="ordering()" class="col-md-4" style="height: 50px; font-size: 18px;
                 text-align: center; line-height: 50px; background-color: #C3E507; color: white; cursor: pointer">
                    去 支 付
                </div>
            </div>
        </div>
    </form>
    @stop

@section('script')
    <script>
        $(function () {
        })
        
        function showAddress() {
            var size = {
                width: '1000px',
                height: '650px'
            }
            var select_address_id = $("input[name='address_id']").val();
            var url = "{{url('/OrderApi/selectAddress')}}?address_id=" + select_address_id;
            openDialog(size, url, "确认地址");
        }

        function refreshBeforeOrdering(address) {
            $("input[name='address_id']").val(address['id'])
            $("#ordering").attr('action', "{{url('OrderApi/beforeOrdering')}}")
            $("#ordering").submit();
        }

        function ordering() {
            var url = "{{url('OrderApi/ordering')}}";
            var data = $("#ordering").serialize();

            $.ajax({
                url: url,
                data: data,
                type: 'post',
                success: function (res) {
                    layer.msg(res.msg)
                    window.location.href = res.url
                }
            })
        }
    </script>
    @stop