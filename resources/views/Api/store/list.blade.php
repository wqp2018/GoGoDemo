@extends('Api.base')

@section('style')
    <style>
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
    <a href="{{url('UserApi/homePage')}}">
        <div style="height:40px; width:40px; border: 1px solid black; border-radius: 50%; text-align: center; line-height: 40px; font-size: 20px">
            <span class="glyphicon glyphicon-arrow-left"></span>
        </div>
    </a>
    <div class="container">
        <h3><span class="glyphicon @if($type == "hot") glyphicon-fire
                        @elseif($type == "recommended") glyphicon-star
                        @endif"></span> {{$types[$type]}}</h3>
        <div class="row">
            @forelse($list['data'] as $k => $v)
                    <div class="col-xs-6 col-md-3">
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
                                <h4 style="display: inline-block;width: 120px"><a href="{{url('UserApi/store')."?id={$v['id']}"}}">{{$v['name']}}</a></h4>
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

        <span style="font-size: 18px">总共有 {{$list['total']}} 条结果，当前为第 {{$list['currentPage']}} 页</span>
        <div style="text-align: center">
            {{ $list['page'] }}
        </div>
    </div>

@stop