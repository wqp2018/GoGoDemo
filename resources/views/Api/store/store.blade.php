@extends('Api.base')

@section('body')
    <div class="container">
        <div class="head-background">
            <div style="text-align: center;padding-top: 40px">
                <img src="{{$data['avatar']}}" style="border-radius: 50%;" height="120px" width="120px"><br />
                <span style="font-size: 18px">{{$data['name']}}</span>
                <div class="link-list" style="margin-top: 18px">
                    <ul class="nav" role="tablist">
                        <li role="presentation" class="active"><a href="#">个人信息</a></li>
                        <li role="presentation"><a href="#">我的订单</a></li>
                        <li role="presentation"><a href="#">信息</a></li>
                        <li role="presentation"><a href="javascript:void(0)" onclick="logout()">退出登录</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @stop