@extends('Api.base')

@section('style')
    <style>
        .container{
            width: 500px;
            height: 900px;
            overflow-y: auto;
            border: 1px solid black;
        }
        .order_div{
            background-color: #F8F5F4;
            padding: 5px;
            height: 150px;
            margin-top: 15px;
        }
        [v-cloak] {
            display: none;
        }
    </style>
    @stop

@section('body')
    <div id="app" v-cloak>
        <div class="container" style="position: relative">
            <div class="head-background">
                <a href="javascript:history.go(-1)">
                    <div style="position: absolute; height:40px; width:40px; border: 1px solid black; border-radius: 50%; text-align: center; line-height: 40px; font-size: 20px">
                        <span class="glyphicon glyphicon-arrow-left"></span>
                    </div>
                </a>
                <a href="{{url('/UserApi/homePage')}}">
                    <div style="position: absolute; right: 10px; font-size: 20px">
                        <span class="">首页</span>
                    </div>
                </a>
                <span style="display: inline-block; height: 40px; line-height: 40px; font-size: 24px; text-align: center; width: 100%">我的订单</span>
            </div>

            <div class="my_order" style="margin-top: 20px">
                <div class="order_div" v-for="item in order_list">
                    <a :href="'{{url('UserApi/store')}}?id=' + item.store_id">
                        <div class="row">
                            <div class="col-md-2">
                                <img :src="item.avatar" height="60px" width="60px">
                            </div>
                            <div class="col-md-6">
                                <span style="font-size: 18px; font-weight: bold">@{{ item.name }}</span><span> > </span><br /><br />
                                <span>@{{ item.create_time }}</span>
                            </div>
                            <div class="col-md-4">
                                <span style="font-size: 14px">@{{ item.order_status_ch }}</span>
                            </div>
                        </div>
                    </a>
                    <a :href="'{{url('OrderApi/orderDetail')}}?order_id=' + item.id">
                        <div class="row">
                            <div class="col-md-2"></div>
                            <div class="col-md-7" style="border-top: 1px solid gray; padding: 10px">
                                <span v-if="item.items.length > 1">
                                    <span>@{{ item.items[0].name }}等@{{ item.items.length }}件商品</span>
                                </span>
                                <span v-else>
                                    <span>@{{ item.items[0].name }}</span>
                                </span>
                            </div>
                            <div class="col-md-2" style="border-top: 1px solid gray; padding: 10px">
                                <span style="line-height: 20px; font-size: 16px">￥@{{ item.actual_payment }}</span>
                            </div>
                        </div>
                    </a>
                    <div class="row">
                        @{{ item.status }}
                        <template v-if="item.order_status < 2">
                            <div class="col-md-6"></div>
                            <div class="col-md-3">
                                <a :href="'{{url('UserApi/store')}}?id=' + item.store_id" class="btn btn-primary" style="display: inline-block">再来一单</a>
                            </div>
                            <div class="col-md-3">
                                <button type="button" @click="cancelOrder(item.id)" class="btn btn-primary" style="display: inline-block">取消订单</button>
                            </div>
                        </template>
                        <template v-else-if="item.order_status == 5">
                            <div class="col-md-8"></div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-default" disabled style="display: inline-block">取消中，待审核</button>
                            </div>
                        </template>
                        <template v-else-if="item.order_status == 6">
                            <div class="col-md-8"></div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-default" disabled style="display: inline-block">已取消订单</button>
                            </div>
                        </template>
                        <template v-else>
                            <div class="col-md-8"></div>
                            <div class="col-md-4">
                                <a :href="'{{url('UserApi/store')}}?id=' + item.store_id" class="btn btn-primary" style="display: inline-block">再来一单</a>
                            </div>
                        </template>

                    </div>
                </div>
            </div>

            <button type="button" class="btn btn-primary" style="width: 100%" v-if="show_count < all_count" @click="showMore()">
                加载更多
            </button>
            <button type="button" class="btn btn-default" disabled style="width: 100%" v-else>
                没有更多了
            </button>
        </div>
    </div>
    @stop

@section('script')
    <script>
        $(function () {

        })
        var page = 0;

        var app = new Vue({
            el: '#app',
            data: {
                order_list: [],
                show_count: 0,
                all_count: 0
            },
            created: function () {
                this.getOrderList()
            },
            methods:{
                getOrderList: function () {
                    var url = "{{url('OrderApi/orderListAjax')}}?page=" + page;
                    $.ajax({
                        url: url,
                        success: function (res) {
                            app.order_list = app.order_list.concat(res.data.order_list)
                            app.show_count += res.data.order_list.length;
                            app.all_count = res.data.all_count
                        }
                    })
                },
                showMore: function () {
                    page++;
                    this.getOrderList();
                },
                cancelOrder: function (order_id) {
                    layer.alert("是否取消该订单？",{
                        btn: ["确认", "取消"],
                        closeBtn: 1,
                        yes: function (index) {
                            var url = "{{url('OrderApi/cancelOrder')}}?order_id="+ order_id
                            $.ajax({
                                url: url,
                                success: function (res) {
                                    if (res.status == 0){
                                        layer.alert(res.msg, {
                                            btn: ['确定']
                                        })
                                    } else {
                                        layer.msg(res.msg)
                                        setTimeout(function () {
                                            window.location.reload()
                                        }, 1500)
                                    }
                                }
                            })
                            layer.close(index)
                        }
                    })
                }
            }
        })
    </script>
    @stop