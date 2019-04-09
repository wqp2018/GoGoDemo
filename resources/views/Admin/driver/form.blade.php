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
    <div id="app_form">
        <form id="form" method="post" url="{{url('Driver/form')}}" enctype="multipart/form-data">
            <h2 class="text-primary">@if(isset($data['id'])) 编辑 @else 新增 @endif 骑手</h2>
            <input type="hidden" name="create_time" value="{{$data['create_time'] or time()}}">
            <input type="hidden" name="update_time" value="{{time()}}">
            <input type="hidden" name="id" value="{{$data['id'] or 0}}">

            <div style="margin-top: 40px">
                <div class="form-item input-group input-group-lg">
                    <span class="input-group-addon" id="sizing-food-name">骑手名称</span>
                    <input type="text" name="name" value="{{$data['name'] or ""}}" class="form-control" aria-describedby="sizing-food-name">
                </div>

                <div class="form-item input-group input-group-lg">
                    <span class="input-group-addon" id="sizing-food-name">联系方式</span>
                    <input type="text" name="phone" value="{{$data['phone'] or ""}}" class="form-control" aria-describedby="sizing-food-name">
                </div>

                <div class="form-item input-group input-group-lg">
                    <span class="input-group-addon" id="sizing-food-name">密码</span>
                    <input type="password" name="pass_word" value="{{$data['pass_word'] or ""}}" class="form-control" aria-describedby="sizing-food-name">
                </div>

                <div class="form-group center-block">
                    <button type="button" class="confirm btn btn-primary">确定</button>
                    <button type="button" class="btn btn-default">返回</button>
                </div>
            </div>

        </form>
    </div>
@stop
@section('script')
    <script>
        $(function () {
        })
    </script>
@stop