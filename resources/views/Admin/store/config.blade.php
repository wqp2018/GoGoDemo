@extends('Admin.public.base')

@section('style')
    <style>
        .form-item{
            margin-bottom: 20px;
        }
    </style>
@stop
@section('head')

@stop
@section('body')
    <div id="app_form">
        <form id="form" method="post" url="{{url('Store/config')}}" enctype="multipart/form-data">

                <div class="form-item input-group input-group-lg">
                    <span class="input-group-addon" id="auto_refuse_order">订单自动拒单时间</span>
                    <input type="text" style="width: 40%" name="auto_refuse_order_time" value="{{$data['auto_refuse_order_time'] or 30}}" class="form-control" aria-describedby="auto_refuse_order">
                </div>

                <div class="form-group center-block">
                    <button type="button" class="confirm btn btn-primary">确定</button>
                    <button type="button" class="btn btn-default">返回</button>
                </div>
            </div>

        </form>
    </div>
@stop