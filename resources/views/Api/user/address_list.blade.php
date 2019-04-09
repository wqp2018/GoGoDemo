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
        <h3>地址管理</h3>
        <a class="btn btn-primary" href="{{url('UserApi/addressForm')}}">新增地址</a>
        <div class="food-list" style="height: 600px;">
                    <div class="list-group">
                        @foreach($address_list as $k => $v)
                            <div class="list-group-item">
                                <div class="row">
                                    <div class="col-md-9">
                                        <span>联系人：{{$v['linkman']}}</span><br />
                                        <span>联系电话：{{$v['phone']}}</span><br />
                                        <span>送达地址：{{$v['address']}}</span><br />
                                        <span>备注：{{$v['remark']}}</span><br />
                                    </div>
                                    <div class="col-md-3">
                                        <a href="{{url('UserApi/addressForm')."?id=".$v['id']}}">修改</a>
                                        <a href="{{url('UserApi/deleteAddress')."?id=".$v['id']}}">删除</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
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

    </script>
@stop