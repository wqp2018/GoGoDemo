@extends('Admin.public.base')

@section('body')
    <div id="app">
        <form method="post" url="{{url('Menus/form')}}">
            <h2 class="text-primary">用户信息</h2>
            <input type="hidden" name="create_time" value="{{$data['create_time'] or time()}}">
            <input type="hidden" name="update_time" value="{{time()}}">
            <input type="hidden" name="id" value="{{$data['id'] or 0}}">

            <div style="margin-top: 40px">
                <div class="form-group has-success has-feedback">
                    <div class="input-group">
                        <span class="input-group-addon">菜单名称</span>
                    </div>
                </div>

                <div class="form-group has-success has-feedback">
                    <div class="input-group">
                        <span class="input-group-addon">菜单地址</span>
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon">上级菜单</span>

                    </div>
                </div>

                <div class="form-group center-block">
                    <button type="button" class="confirm btn btn-primary">确定</button>
                    <button type="button" class="btn btn-default">返回</button>
                </div>
            </div>

        </form>
    </div>
@stop
@section('head')
    <script>
        $(function () {


        })

        var  app = new Vue({
            el: "#app",
            data: {},
            created: function () {

            },
            methods: {

            }
        })


    </script>
@stop