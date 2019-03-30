@extends('Api.base')

@section('style')
    <style>
        .container{
            width: 500px;
            height: 900px;
            border: 1px solid gray;
        }
        .food-list{
            width: 470px;
            height: 600px;
            margin-top: 40px;
        }
        .list-group li{
            height: 100px;
        }
        #myTabContent{
            height: 530px;
            overflow-y: auto;
            overflow-x: hidden;
        }
        .addOrReduce{
            display: inline-block;
            font-size: 20px;
            cursor: pointer;
        }
        .container{
            overflow: hidden;
        }
        a:hover{
            text-decoration: none;
        }
        [v-cloak] {
            display: none;
        }
    </style>
    @stop

@section('body')

    <form id="form" action="{{url('OrderApi/beforeOrdering')}}" method="post">
        {{ csrf_field() }}
        <div id="app" v-cloak class="container">
        <div class="head-background">
            <a href="{{url('UserApi/homePage')}}">
                <div style="position: absolute; height:40px; width:40px; border: 1px solid black; border-radius: 50%; text-align: center; line-height: 40px; font-size: 20px">
                    <span class="glyphicon glyphicon-arrow-left"></span>
                </div>
            </a>
            <div style="text-align: center;padding-top: 20px;height: 40%;">
                <img src="{{$data['avatar']}}" style="border-radius: 50%;" height="120px" width="120px"><br />
                <span style="font-size: 18px">{{$data['name']}}</span><br>
                <span style="font-size: 16px; color: red;">
                    @if($data['abnormal_status'] == 0)
                        营业中<br /><br />
                        @elseif($data['abnormal_status'] == 1)
                            超出配送范围<br /><br />
                        @elseif($data['abnormal_status'] == 2)
                            休息中<br />(营业时间{{$data['tip_business_time']}})
                    @endif
                </span>
            </div>
            <div class="food-list" style="height: 600px;">
                <ul style="text-align: center" id="myTab" class="nav nav-tabs">
                    <li class="active" style="width: 50%">
                        <a href="#home" data-toggle="tab">点餐</a>
                    </li>
                    <li style="width: 50%"><a href="#store" data-toggle="tab">商家</a></li>
                </ul>
                <div id="myTabContent" class="tab-content">
                    <div class="tab-pane fade in active" id="home">
                        <div class="list-group">
                            <div class="list-group-item" v-for="item in foods">
                                <div style="height: 80px; width: auto; position: relative">
                                    <div style="display: inline-block;float: left; height: 80px; width: 80px">
                                        <img :src="item.avatar" height="80px" width="80px">
                                    </div>
                                    <div style="display: inline-block;float: left;margin-left: 11px; height: 80px; width: 70%;">
                                        <span style="font-size: 16px">@{{ item.name }}</span><br>
                                        <span style="display: inline-block;width: 60%;color: gray;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;">@{{ item.items }}</span><br>
                                        <span style="color: gray;">销售 <span style="color: red">@{{ item.total_sale }} </span>份 <span>库存 @{{ item.inventory }} 份</span></span> <br>
                                        <div>
                                            <span style="color: red; font-weight: bold">￥@{{item.price}}</span>
                                            <div style="display: inline-block; position: absolute; right: 1%; width: 100px; height: 25px;">
                                                <a href="javascript:void(0)" @click="reduceFood(item)"><span class="glyphicon glyphicon-minus-sign addOrReduce"></span></a>
                                                <input v-model="item.count" style="display: inline-block; height: 20px; width: 50px; left: 25px; position: absolute; text-align: center" type="text" @change="changeFood(item, this.value)">
                                                <a href="javascript:void(0)" @click="addFood(item)"><span class="glyphicon glyphicon-plus-sign addOrReduce" style="position: absolute;right: 0"></span></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" style="padding-left: 10px" id="store">
                        <h3>商家信息</h3>
                        <br>
                        <span>{{$data['name']}}</span>
                        <hr />
                        <div>
                            <span style="font-weight: bold;font-size: 16px">商家电话</span>
                            <span style="float: right;margin-right: 20px">{{$data['phone']}}</span>
                        </div>
                        <hr />
                        <div>
                            <span style="font-weight: bold;font-size: 16px">地址</span>
                            <span style="float: right;margin-right: 20px">{{$data['address']}}</span>
                        </div>
                        <hr />
                        <div>
                            <span style="font-weight: bold;font-size: 16px">营业时间</span>
                            <span style="float: right;margin-right: 20px">{{$data['business_time']}}</span>
                        </div>
                    </div>
                </div>
            </div>

            @if($data['abnormal_status'] == 0)
                <div class="row" style="height: 50px; width: auto;position: relative">
                    <div class="col-md-9" style="background-color: #336666; z-index: 999">
                        <div onclick="showSelectFood()"  style="cursor: pointer; display: inline-block; height: 50px; width: 50px; border-radius: 50%; background-color: black; text-align: center;">
                            <div style="display: inline-block; height: 42px; width: 42px; border-radius: 50%; background-color: #1E9FFF; margin-top: 4px;
                     text-align: center; line-height: 52px; color: white;">
                                <span class="glyphicon glyphicon-shopping-cart"style="font-size: 20px;"></span>
                            </div>
                            <span v-if="all_count != 0">
                            <span class="badge" style="display: inline-block; position: absolute; left: 5px">@{{ all_count }}</span>
                        </span>
                        </div>

                        <span style="margin-left: 20px"></span>
                        <div style="display:inline-block;">
                            <span v-if="all_count==0" style="color: #D1F2EB">未选择任何商品</span>
                            <div v-else style="color: white;">
                                <span style="font-size: 20px; font-weight: bold;">￥@{{ all_price }}</span>
                                <span style="font-size: 12px;">另需配送费{{ $data['delivery_fee'] }}元</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3" style="z-index: 999;height: 50px; display: inline-block; color: white;
                     background-color: #E8A010; text-align: center; line-height: 50px; cursor: pointer" @click="beforeOrder()">去结算</div>
                    <input name="store_id" value="{{$data['id']}}" hidden>
                    <input name="select_food" :value="select_food_json" hidden>
                </div>
            @elseif($data['abnormal_status'] == 1)
                <div class="row" style="height: 50px; width: auto;background-color: #CCD1D1">
                    <div class="col-md-12" style="text-align: center; line-height: 50px">
                        <span style="font-size: 14px">无法配送，超出配送范围</span>
                    </div>
                </div>
                @else
                <div class="row" style="height: 50px; width: auto;background-color: #CCD1D1">
                    <div class="col-md-12" style="text-align: center; line-height: 50px">
                        <span style="font-size: 14px">店家还未营业，不可下单</span>
                    </div>
                </div>
                @endif

            <div class="select_food_list" style="position: relative; width: auto; padding: 0; margin: 0; z-index: 2">
                <div style="background-color: #CCFF66; font-size: 15px; height: 36px; line-height: 36px">
                    <span style="margin-left: 20px">已选商品</span>
                    <div style="display: inline-block;position: absolute; right: 20px">
                        <a href="javascript:void(0)" @click="clearSelectFood()"><span class="glyphicon glyphicon-trash"></span><span>清空</span></a>
                    </div>
                </div>
                <div class="row" style="height: 200px; overflow-y: auto; background-color: #FFFF99">
                    <div class="col-md-12" style="height: 50px; width: 100%;" v-for="item in select_food">
                        <div style="height: 50px; width: auto; position: relative; padding-top: 15px">
                            <div style="display: inline-block; height: auto; width: 60%">
                                <span style="color: red; font-weight: bold">@{{item.name}}</span>
                            </div>
                            <span style="color: red; font-weight: bold; width: 15%;">￥@{{item.price * item.count}}</span>
                            <div style="display: inline-block; position: relative; width: 100px; height: 25px; position: absolute;right: 2%">
                                <a href="javascript:void(0)" @click="reduceFood(item)"><span class="glyphicon glyphicon-minus-sign addOrReduce"></span></a>
                                <input style="display: inline-block; height: 20px; width: 50px; left: 25px; position: absolute; text-align: center" type="text" v-model="item.count" @change="changeFood(item)">
                                <a href="javascript:void(0)" @click="addFood(item)"><span class="glyphicon glyphicon-plus-sign addOrReduce" style="position: absolute;right: 0"></span></a>
                            </div>
                        </div>
                        <div style="background-color:blue;height:1px;border:none;"></div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    </form>
    @stop
@section('script')
    <script>
        $(function () {
        })
        var show = false;

        function showSelectFood() {
            if (show == false){
                $(".select_food_list").animate({
                    top: "-286px"
                })
                show = true;
            } else {
                $(".select_food_list").animate({
                    top: 0
                })
                show = false;
            }

        }

        function resetAllPrice() {
            var price = 0;
            for (var i in app.select_food){
                price += app.select_food[i]['count'] * app.select_food[i]['price'];
            }
            app.all_price = price;
            app.select_food_json = JSON.stringify(app.select_food)
            setCookie();
        }

        function setCookie() {
            var json = JSON.stringify(app.select_food)
            $.cookie('select_food', json)
        }

        var app = new Vue({
            el: '#app',
            data: {
                foods: [],
                select_food: [],
                select_food_json: "",
                all_count: 0,
                all_price: 0
            },
            created: function () {
                this.getStoreFoods();
            },
            mounted: function(){
            },
            methods:{
                getCookie: function(){
                    var select_food = $.cookie('select_food');
                    if (select_food != "null"){
                        app.select_food = JSON.parse(select_food)
                        $.each(app.select_food, function (index, value) {
                            $.each(app.foods, function (i, v) {
                                if (value['id'] == v['id']){
                                    v['count'] = value['count'];
                                    app.all_count += value['count']
                                }
                            })
                        })
                        resetAllPrice()
                    }
                },
                getStoreFoods: function () {
                   var url = "{{url('UserApi/storeFood')}}";
                   var data = {
                     store_id: "{{$data['id']}}"
                   };
                   $.ajax({
                       url: url,
                       data: data,
                       type: "get",
                       success: function (res) {
                           for (var i in res.data){
                               res.data[i]['count'] = 0;
                           }
                           app.foods = res.data;
                           app.getCookie()
                       }
                   })
               },
                reduceFood: function (item) {
                   item['count']--;
                   if (item['count'] < 0){
                       item['count'] = 0;
                   }

                   for (var i in app.select_food){
                       if (item['id'] == app.select_food[i]['id']){
                           app.select_food[i] = item;
                           if (item['count'] == 0){
                               app.select_food.splice(i, 1)
                           }
                           break;
                       }
                   }
                   app.all_count -= 1;
                   if (app.all_count < 0){
                       app.all_count = 0;
                   }
                   resetAllPrice()
               },
                addFood: function (item) {
                   item['count']++;

                   var status = false;
                   for (var i in app.select_food){
                       if (item['id'] == app.select_food[i]['id']){
                           status = true;
                           app.select_food[i] = item;
                           break;
                       }
                   }
                   if (status === false){
                       app.select_food.push(item)
                   }
                   app.all_count += 1;
                   resetAllPrice()
               },
                changeFood: function (item) {
                   var status = false;
                   var count = 0;
                   item['count'] = parseInt(item['count'])
                   for (var i in app.select_food){
                       count += app.select_food[i]['count'];
                       if (item['id'] == app.select_food[i]['id']){
                           app.select_food[i] = item;
                           status = true;
                       }
                   }
                   if (status === false){
                       app.select_food.push(item)
                       count += item['count'];
                   }
                   app.all_count = count
                   resetAllPrice()
               },
                clearSelectFood: function () {
                   app.select_food = [];
                   app.all_count = 0;
                   app.all_price = 0;
                   for(var i in app.foods){
                       app.foods[i]['count'] = 0;
                   }
               },
                beforeOrder: function () {
                   $("#form").submit();
                }
            }
        })
    </script>
    @stop