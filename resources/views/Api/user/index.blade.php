@extends('Api.base')

@section('style')
    <style>
    body{
        background: skyblue;
    }
    .head-background{
        background: url('/uploads/images/background.jpg') no-repeat center;
        background-size: 100%;
        height: 400px;
    }
    .store-list{
        margin-top: 20px;
    }
    .link-head{
        width:  100%;
        height: 40px;
        font-size: 18px;
        line-height: 40px;
        background: white;
    }
    .show_address{
        height: 30px;
        min-width: 100px;
        margin: auto;
        text-align: center;
        font-size: 20px;
        font-weight: bold;
    }
    .covering{
        height: 200px;
        width: 200px;
        position: absolute;
        color: white;
        font-size: 16px;
        background: grey;
        text-align: center;
        opacity: 0.8;
    }
    .show_tip{
        margin: auto;
        margin-top: 80px;
        display: inline-block;
    }
    </style>
@stop
@section('body')
    <div class="container">
        <div class="user-info">
            <div class="head-background">
                <div class="show_address">
                    <span>地址 : <a href="#">{{$user['address']}}</a></span>
                </div>
                <div style="text-align: center;padding-top: 40px">
                    <img src="{{$user['avatar']}}" style="border-radius: 50%;" height="120px" width="120px"><br />
                    <span style="font-size: 18px">{{$user['user_name']}}</span>
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

            <div class="store-list">
                <div class="hot-store">
                    <div class="link-head" style="position: relative">
                        <div style="display: inline-block; padding-left: 20px"><span class="glyphicon glyphicon-fire"></span> 热门餐厅</div>
                        <div style="display: inline-block; position: absolute; left: 90%"><a href="{{url('UserApi/hot')}}">更多 >></a></div>
                    </div>
                    <div class="row">
                        @forelse($hot_store['data'] as $k => $v)
                            <div class="col-sm-4 col-md-3">
                                <div class="thumbnail" style="display: inline-block">
                                    <div>
                                        <div class="covering" @if($v['abnormal_status'] == 0) hidden @endif>
                                            @if($v['abnormal_status'] == 1)
                                                <span class="show_tip">已超出配送范围</span>
                                            @elseif($v['abnormal_status'] == 2)
                                                <span class="show_tip">休息中<br />(营业时间:{{$v['tip_business_time']}})</span>
                                            @endif
                                        </div>
                                        <img src="{{$v['avatar']}}" height="200px" width="200px">
                                    </div>
                                    <div class="caption">
                                        <h4 style="display: inline-block;width: 120px">{{$v['name']}}</h4>
                                        <span style="color: #b57c5b;font-size: 18px;">销量:{{$v['total_sale']}}</span>
                                    </div>
                                    <div>
                                        <span style="color: #666666">{{round($v['distance'] / 1000,2)}}km</span>
                                        <span style="float: right">配送费 : {{$v['delivery_fee']}}元</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-sm-4 col-md-3">
                                <h3>没有找到对应商店。</h3>
                            </div>
                        @endforelse
                    </div>
                </div>
                <div class="recommended-store">
                    <div class="link-head" style="position: relative">
                        <div style="display: inline-block; padding-left: 20px"><span class="glyphicon glyphicon-star"></span> 推荐餐厅</div>
                        <div style="display: inline-block; position: absolute; left: 90%"><a href="{{url('UserApi/recommended')}}">更多 >></a></div>
                    </div>
                    <div class="row">
                        @forelse($recommended_store['data'] as $k => $v)
                            <div class="col-sm-4 col-md-3">
                                    <div class="thumbnail" style="display: inline-block">
                                        <div>
                                            <div class="covering" @if($v['abnormal_status'] == 0) hidden @endif>
                                                @if($v['abnormal_status'] == 1)
                                                    <span class="show_tip">已超出配送范围</span>
                                                @elseif($v['abnormal_status'] == 2)
                                                    <span class="show_tip">休息中<br />(营业时间:{{$v['tip_business_time']}})</span>
                                                    @endif
                                            </div>
                                            <img src="{{$v['avatar']}}" height="200px" width="200px">
                                        </div>
                                        <div class="caption">
                                            <h4 style="display: inline-block;width: 120px">{{$v['name']}}</h4>
                                            <span style="color: #b57c5b;font-size: 18px;">销量:{{$v['total_sale']}}</span>
                                        </div>
                                        <div>
                                            <span style="color: #666666">{{round($v['distance'] / 1000,2)}}km</span>
                                            <span style="float: right">配送费 : {{$v['delivery_fee']}}元</span>
                                        </div>
                                    </div>
                            </div>
                        @empty
                            <h3>没有找到对应商店。</h3>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('script')
<script>
    $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});

    $(function () {

    })
    function logout() {
        layer.confirm("是否确认退出？",{
            btn: ['确认', '取消']
        },function () {
            var url = "{{url('UserApi/logout')}}"
            $.ajax({
                url: url,
                type: "post",
                success: function (res) {
                    if (res.status == 1){
                        layer.msg("退出登录成功")
                        setTimeout(function () {
                            window.location.href = "{{url('/login')}}"
                        },1000)
                    }else {
                        layer.alert("发生不知名错误",{
                            btn: ['确定']
                        })
                    }
                }
            })
        })
    }
</script>
    @stop