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
        <form id="form" method="post" url="{{url('Food/form')}}" enctype="multipart/form-data">
            <h2 class="text-primary">@if(isset($data['id'])) 编辑 @else 新增 @endif 餐点</h2>
            <input type="hidden" name="create_time" value="{{$data['create_time'] or time()}}">
            <input type="hidden" name="update_time" value="{{time()}}">
            <input type="hidden" name="id" value="{{$data['id'] or 0}}">
            <input type="hidden" name="store_id" value="{{$store_id or 0}}">

            <div style="margin-top: 40px">
                <div class="form-item">
                    <h3>餐点图片 : </h3>
                    <div style="height: 120px; width: 120px;margin-bottom: 10px">
                        <img id="image_show" style="height: 120px; width: 120px;" src="{{$data['avatar'] or ""}}" onerror="this.src='{{'/uploads/images/add_image.jpg'}}'">
                    </div>
                    <input class="uploadImage hidden" type="file" name="image" value="">
                    <input type="hidden" name="avatar" value="{{$data['avatar'] or ""}}" />
                    <button class="btn btn-primary" onclick="uploadImage()" type="button">上传图片</button>
                </div>

                <div class="form-item input-group input-group-lg">
                    <span class="input-group-addon" id="sizing-food-name">餐点名称</span>
                    <input type="text" name="name" value="{{$data['name'] or ""}}" class="form-control" aria-describedby="sizing-food-name">
                </div>

                <div class="form-item input-group input-group-lg">
                    <span class="input-group-addon" id="sizing-food-price">价格</span>
                    <input type="text" name="price" value="{{$data['price'] or ""}}" class="form-control" aria-describedby="sizing-food-price">
                </div>

                <div class="form-item input-group input-group-lg">
                    <span class="input-group-addon" id="sizing-food-inventory">库存</span>
                    <input type="text" name="inventory" value="{{$data['inventory'] or ""}}" class="form-control" aria-describedby="sizing-food-inventory">
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