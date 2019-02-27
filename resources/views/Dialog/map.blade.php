@extends('Dialog.base')

@section('body')
    <div style="padding: 15px">
        输入地址名称 : <input name="address" style="display: inline-block;width: 40%" type="text" class="form-control" />
        <button class="btn btn-primary" onclick="searchAddress()">搜索</button>
    </div>

    <div id="container" style="width:1000px; height:450px;"></div>

    <div style="margin-top: 20px">
        <button class="btn btn-primary" onclick="submitAddress()">确定</button>
        <button class="btn btn-primary">取消</button>
        <span class="address_info" style="margin-left: 10px;font-size: 18px"></span>
    </div>
    @stop

@section('script')
    <script charset="utf-8" src="https://map.qq.com/api/js?v=2.exp&key=22YBZ-NQXKP-Z32DJ-LZMTH-QP4W2-RDBGV"></script>
    <script>
        var citylocation,map,marker,geocoder = null;
        var markersArray = [];
        var latLng, street = null;
        $(function () {
            // 初始化地图
            init();
            //添加监听事件   获取鼠标单击事件
            qq.maps.event.addDomListener(map, 'click', function(event) {
                clearMarker()
                getAddress(event.latLng)
                addMarker(event.latLng);
            });
        })
        function init() {
            var center = new qq.maps.LatLng(39.916527,116.397128);
            map = new qq.maps.Map(document.getElementById('container'),{
                center: center,
                zoom: 13
            });
            //获取城市列表接口设置中心点
            citylocation = new qq.maps.CityService({
                complete : function(result){
                    map.setCenter(result.detail.latLng);
                    getAddress(result.detail.latLng)
                    addMarker(result.detail.latLng)
                }
            });
            geocoder = new qq.maps.Geocoder({
                complete : function(result){
                    latLng = result.detail.location
                    //中国，广州，白云区，嘉禾望岗
                    street = result.detail.address;
                    $(".address_info").text("纬度：" + latLng.lat + "，经度: " + latLng.lng + "，地址: " + street);
                    map.setCenter(result.detail.location);
                    addMarker(result.detail.location)
                }
            });
            //调用searchLocalCity();方法    根据用户IP查询城市信息。
            citylocation.searchLocalCity();
        }

        // 根据名称查找地址
        function searchAddress() {
            var address = $("input[name='address']").val();
            geocoder.getLocation(address);
        }
        
        //  根据经纬度查找地址
        function getAddress(latLng) {
            geocoder.getAddress(latLng)
        }

        // 添加标注
        function addMarker(latLng) {
            marker = new qq.maps.Marker({
                position: latLng,
                map: map
            });
            markersArray.push(marker);
        }
        
        // 清除标注
        function clearMarker() {
            if (markersArray) {
                for (i in markersArray) {
                    markersArray[i].setMap(null);
                }
            }
        }

        function submitAddress() {
            var index=parent.layer.getFrameIndex(window.name);
            parent.layer.close(index);

            parent.setAddress(latLng, street)
        }
    </script>
    @stop