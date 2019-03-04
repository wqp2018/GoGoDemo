@extends('Api.base')

@section('style')
    .form-item{
    margin-bottom: 20px;
    }
    @stop

@section('body')
    <div class="container">
        <div class="form-group">
            <h3>@if(isset($data['id']) && $data['id'] != 0) 编辑 @else 新增 @endif 地址</h3>
        </div>

        <form id="form" method="post" url="{{url('UserApi/addressForm')}}">
            <input type="hidden" name="create_time" value="{{$data['create_time'] or time()}}">
            <input type="hidden" name="update_time" value="{{time()}}">
            <input type="hidden" name="id" value="{{$data['id'] or 0}}">

            <div class="form-item input-group input-group-lg">
                <span class="input-group-addon" id="sizing-store-address">联系人</span>
                <input type="text" name="linkman" value="{{$data['linkman'] or ""}}" class="form-control" aria-describedby="sizing-store-address">
            </div>

            <div class="form-item input-group input-group-lg">
                <span class="input-group-addon" id="sizing-store-address">联系电话</span>
                <input type="text" name="phone" value="{{$data['phone'] or ""}}" class="form-control" aria-describedby="sizing-store-address">
            </div>

            <div class="form-item input-group input-group-lg">
                <span class="input-group-addon" id="sizing-store-address">详细地址</span>
                <input type="text" name="address" value="{{$data['address'] or ""}}" class="form-control" aria-describedby="sizing-store-address">
            </div>

            <div class="form-item input-group input-group-lg">
                <span class="input-group-addon">选择所在区域</span>
                <select class="form-control" style="width: 20%" id="first-city">
                    <option value="0">请选择</option>
                    @foreach($data['city'] as $k => $v)
                        <option value="{{$v['id']}}" @if($v['id'] == $data['next_next_city']) selected @endif>{{$v['name']}}</option>
                    @endforeach
                </select>
                <select class="form-control" style="width: 20%" id="second-city">
                    <option value="0">请选择</option>
                </select>
                <select name="city_id" class="form-control" style="width: 20%" id="third-city">
                    <option value="0">请选择</option>
                </select>
            </div>

            <div class="form-item input-group input-group-lg">
                <span class="input-group-addon">经 纬 度</span>
                <input class="input-lg" type="text" readonly placeholder="纬度" name="lat" value="{{$data['lat'] or ""}}"> -
                <input class="input-lg" type="text" readonly placeholder="经度" name="lng" value="{{$data['lng'] or ""}}">
                <button style="margin-left: 20px" type="button" class="btn btn-primary" onclick="getMap()">获取店家地址信息</button>
            </div>

            <div class="form-item input-group input-group-lg">
                <span class="input-group-addon" id="sizing-store-remark">备注信息</span>
                <input type="text" name="remark" value="{{$data['remark'] or ""}}" class="form-control" aria-describedby="sizing-store-remark">
            </div>

            <div class="form-group center-block">
                <button type="button" class="confirm btn btn-primary">确定</button>
                <button type="button" class="btn btn-default">返回</button>
            </div>
        </form>
    </div>
    @stop
@section('script')
    <script>
        $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
        $(function () {
            if ($("#first-city option:selected").val() != 0){
                changeSecondLevelCity("{{$data['next_next_city']}}")
            }

            $("#first-city").change(function () {
                changeSecondLevelCity()
            })

            $("#second-city").change(function () {
                changeThirdLevelCity()
            })
        })

        function changeSecondLevelCity() {
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
                    var select_id = "{{$data['next_city'] ?? 0}}";
                    for (var i in data){
                        if (data[i].id == select_id){
                            $("#second-city").append("<option value='"+data[i].id+"' selected>"+data[i].name+"</option>");
                        } else {
                            $("#second-city").append("<option value='"+data[i].id+"'>"+data[i].name+"</option>");
                        }
                        changeThirdLevelCity()
                    }
                }
            })
        }

        function changeThirdLevelCity() {
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
                    var select_id = "{{$data['city_id'] ?? 0}}"
                    for (var i in data){
                        if (data[i].id == select_id){
                            $("#third-city").append("<option value='"+data[i].id+"' selected>"+data[i].name+"</option>");
                        } else {
                            $("#third-city").append("<option value='"+data[i].id+"'>"+data[i].name+"</option>");
                        }
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

        //  弹窗
        function openDialog(size, url, title) {
            if (title == ""){
                title = "信息"
            }
            layer.open({
                type: 2,
                area: [size.width, size.height],
                title: title,
                fixed: false, //不固定
                maxmin: true,
                content: url
            });
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