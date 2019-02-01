@extends('public.base')

@section('body')
    <div id="app">
        <form method="post" url="{{url('Menus/form')}}">
            <h2 class="text-primary">@if(isset($data['id']) && $data['id'] > 0) 编辑 @else 新增 @endif 菜单 </h2>
            <input type="hidden" name="create_time" value="{{$data['create_time'] or time()}}">
            <input type="hidden" name="update_time" value="{{time()}}">
            <input type="hidden" name="id" value="{{$data['id'] or 0}}">

            <div style="margin-top: 40px">
                <div class="form-group has-success has-feedback">
                    <div class="input-group">
                        <span class="input-group-addon">菜单名称</span>
                        <input name="name" value="{{$data['name'] or ""}}" type="text" class="form-control" aria-describedby="inputGroupSuccess1Status">
                    </div>
                </div>

                <div class="form-group has-success has-feedback">
                    <div class="input-group">
                        <span class="input-group-addon">菜单地址</span>
                        <input name="url" value="{{$data['url'] or ""}}" type="text" class="form-control" aria-describedby="inputGroupSuccess1Status">
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon">上级菜单</span>
                        <select name="parent_id" class="form-control">
                            <option value="0">顶级菜单</option>
                            @foreach($newMenus as $k => $v)
                                <option value="{{$v['id']}}">{!! $v['name'] !!}</option>
                                @endforeach
                        </select>
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