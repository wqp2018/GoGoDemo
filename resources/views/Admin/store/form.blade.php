@extends('Admin.public.base')

@section('style')
    <style>
        .form-item{
            margin-bottom: 20px;
        }
        #app_form{
            overflow: auto;
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
        <form id="form" method="post" url="{{url('Store/form')}}" enctype="multipart/form-data">
            <h2 class="text-primary">@if(isset($data['id'])) 编辑 @else 新增 @endif 店家</h2>
            <input type="hidden" name="create_time" value="{{$data['create_time'] or time()}}">
            <input type="hidden" name="update_time" value="{{time()}}">
            <input type="hidden" name="id" value="{{$data['id'] or 0}}">

            <div style="margin-top: 40px">
                <div class="form-item">
                    <h3>店家图片 : </h3>
                    <div style="height: 120px; width: 120px;margin-bottom: 10px">
                        <img id="image_show" style="height: 120px; width: 120px;" src="" onerror="this.src='{{'/uploads/images/add_image.jpg'}}'">
                    </div>
                    <input class="uploadImage hidden" type="file" name="avatar">
                    <button class="btn btn-primary" onclick="uploadImage()" type="button">上传图片</button>
                </div>

                <div class="form-item input-group input-group-lg">
                    <span class="input-group-addon" id="sizing-store-name">店家名称</span>
                    <input type="text" name="name" class="form-control" aria-describedby="sizing-store-name">
                </div>

                <div class="form-item input-group input-group-lg">
                    <span class="input-group-addon" id="sizing-store-phone">联系电话</span>
                    <input type="text" name="phone" class="form-control" aria-describedby="sizing-store-phone">
                </div>

                <div class="form-item input-group input-group-lg">
                    <span class="input-group-addon" id="sizing-store-address">店家地址</span>
                    <input type="text" name="address" class="form-control" aria-describedby="sizing-store-address">
                </div>

                <ul>
                    <li v-for="item in this.firstLevelCity">1</li>
                </ul>
                <div class="form-item input-group input-group-lg">
                    <span class="input-group-addon">店家所在地</span>
                    <select class="form-control" style="width: 20%" id="first-city">
                        <option value="0">请选择</option>
                        <option v-for="item in app_form.firstLevelCity">1</option>
                    </select>
                    <select class="form-control" style="width: 20%" id="second-city">
                        <option value="0">请选择</option>
                    </select>
                    <select name="city" class="form-control" style="width: 20%" id="third-city">
                        <option value="0">请选择</option>
                    </select>
                </div>

                <div class="form-item input-group input-group-lg">
                    <span class="input-group-addon">经 纬 度</span>
                    <input class="input-lg" type="text" readonly placeholder="纬度" name="lat"> -
                    <input class="input-lg" type="text" readonly placeholder="经度" name="lng">
                    <button style="margin-left: 20px" type="button" class="btn btn-primary" onclick="getMap()">获取店家地址信息</button>
                </div>

                <div class="form-item input-group input-group-lg">
                    <span class="input-group-addon">支付方式</span>
                    <div style="font-size: 24px;line-height: 60px">
                        <input style="margin-left: 20px" type="checkbox" name="pay_type" value="1">现金
                        <input type="checkbox" name="pay_type" value="2">支付宝
                    </div>
                </div>

                <div class="form-item input-group input-group-lg">
                    <span class="input-group-addon">营<br>业<br>时<br>间</span>
                    <div class="form-item input-group input-group-lg">
                        <span class="input-group-addon" id="sizing-addon1">第一个营业时间段</span>
                        <input type="text" style="display: inline-block;width: 40%;" class="input-lg form_datetime" name="first_begin_time" > --
                        <input type="text" style="display: inline-block;width: 40%;" class="input-lg form_datetime" name="first_end_time">
                    </div>
                    <div style="margin-bottom: 0px" class="form-item input-group input-group-lg">
                        <span class="input-group-addon" id="sizing-addon2">第二个营业时间段</span>
                        <input type="text" style="display: inline-block;width: 40%" class="input-lg form_datetime" name="second_begin_time" aria-describedby="sizing-addon2"> --
                        <input type="text" style="display: inline-block;width: 40%" class="input-lg form_datetime" name="second_end_time select-time-end" aria-describedby="sizing-addon2">
                    </div>
                </div>

                <div class="form-item input-group input-group-lg">
                    <span class="input-group-addon" id="sizing-store-delivery-range">配送范围 (米)</span>
                    <input type="text" style="width: 40%" name="delivery_range" class="form-control" aria-describedby="sizing-store-delivery-range">
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
            $('.form_datetime').datetimepicker({
                format: 'HH:ii',
                language: "zh-CN",
                startView: "day",
                autoclose: true
            });

            // 获取一级城市
            getFirstLevelCity()

            $("#first-city").change(function () {
                var parent_id = $("#first-city option:selected").val();
                var url = "{{url('/City/nextLevelCity')}}"
                $.ajax({
                    url: url,
                    data: {
                        parent_id: parent_id
                    },
                    success: function (res) {
                        $("#second-city").empty();
                        $("#second-city").append("<option value='"+0+"'>"+"请选择"+"</option>");
                        var data = res.info
                        for (var i in data){
                            $("#second-city").append("<option value='"+data[i].id+"'>"+data[i].name+"</option>");
                            $("#second-city option").eq(1).attr("selected", "selected")
                        }
                    }
                })
            })

            $("#second-city").change(function () {
                var parent_id = $("#second-city option:selected").val();
                var url = "{{url('/City/nextLevelCity')}}"
                $.ajax({
                    url: url,
                    data: {
                        parent_id: parent_id
                    },
                    success: function (res) {
                        $("#third-city").empty();
                        $("#third-city").append("<option value='"+0+"'>"+"请选择"+"</option>");
                        var data = res.info
                        for (var i in data){
                            $("#third-city").append("<option value='"+data[i].id+"'>"+data[i].name+"</option>");
                            $("#third-city option").eq(1).attr("selected", "selected")
                        }
                    }
                })
            })
        })

        function getFirstLevelCity() {
            var url = "{{url('/City/firstLevelCity')}}"
            $.ajax({
                url: url,
                async: false,
                success: function (res) {
                    var data = res.info;
                    for (var i in data){
                        $("#first-city").append("<option value='"+data[i].id+"'>"+data[i].name+"</option>");
                    }
                }
            })
        }

        function getMap() {
            var size = {
                width: '1000px',
                height: '650px'
            }
            var url = "{{url('/Store/map')}}";
            openDialog(size, url, "地址选择");
        }

        function setAddress(latLng, street){
            $("input[name='lat']").val(latLng.lat)
            $("input[name='lng']").val(latLng.lng)
            var address = $("input[name='address']").val();
            // 若输入地址不为空，则询问是否需要覆盖
            if (address != ""){
                layer.confirm('是否使用地图地址覆盖输入地址？', {
                    btn: ['是','否'] //按钮
                }, function(){
                    $("input[name='address']").val(street);
                    layer.msg('地址已覆盖', {icon: 1});
                });
            }else {
                $("input[name='address']").val(street);
            }
        }

    </script>
@stop