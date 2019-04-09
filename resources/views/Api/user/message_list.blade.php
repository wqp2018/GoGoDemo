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
        <a href="{{url('UserApi/homePage')}}">
            <div style="height:40px; width:40px; border: 1px solid black; border-radius: 50%; text-align: center; line-height: 40px; font-size: 20px">
                <span class="glyphicon glyphicon-arrow-left"></span>
            </div>
        </a>
        <h3>消息管理</h3>
        <div class="food-list" style="height: 600px;">
            <div class="list-group">
                @foreach($message_list as $k => $v)
                    <div class="list-group-item">
                        <div class="row">
                            <div class="col-md-9">
                                <span>{{$v['message']}}</span>&nbsp;<span style="color:gray;font-size: 10px">{{date('Y-m-d', $v['create_time'])}}</span>
                            </div>
                            <div class="col-md-3">
                                <a href="{{url('UserApi/deleteMessage')."?id=".$v['id']}}">删除</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>
@stop

@section('script')
    <script>
        $(function () {

        })
        var page = 0;

    </script>
@stop