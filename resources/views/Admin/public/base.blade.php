<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>GoGo外卖平台</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">

    <script type="text/javascript" src="{{ asset('/js/jquery.min.js')  }}"></script>
    <script type="text/javascript" src="{{ asset('/js/layer.js')  }}"></script>
    <script type="text/javascript" src="{{ asset('/js/vue.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/js/bootstrap.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/js/jquery.cookie.js') }}"></script>
    @section('head')
        @show
    <title>Document</title>
</head>
<style>
    body{
        /*overflow: hidden;*/
    }
    [v-cloak] {
        display:none
    }
    .collapse{
        font-size: 18px;
    }
   .left-row{
       margin-top: -20px;
       font-size: 15px;
       background: #343434;
       min-height: 900px;
   }
   .child-menus{
       text-align: center;
       padding: 10px;
   }
   .child-menus a{
       color: white;
   }
</style>
@section('style')
    @show
<body>
    <div id="app" v-cloak>
        {{-- 顶部菜单栏 --}}
        <nav class="navbar navbar-inverse">
            <div class="container-fluid">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <a class="navbar-brand" href="{{url('/')}}">GoGo</a>
                </div>

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav">
                        <li v-for="item in menus"><a class="menus" :menu_id="item.id" :href="item.url">@{{ item.name }}</a></li>
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <li><a href="#">Link</a></li>
                    </ul>
                </div><!-- /.navbar-collapse -->
            </div><!-- /.container-fluid -->
        </nav>

        {{-- 左侧菜单栏 --}}
        <div class="row">
            <div class="col-xs-2 left-row">
                <ul class="nav nav-pills nav-stacked" style="margin-top: 20px">
                    <li class="child-menus" v-for="item in childrenMenus"><a class="childrenMenus" :href="item.url">@{{ item.name }}</a></li>
                </ul>
            </div>
            <div class="col-xs-9">
                @section('body')
                @show
            </div>
        </div>

    </div>
</body>
<script>
    $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
    // 进入新页面时清空cookie
    // $.cookie('select_ids', null);
    $(function () {

        $(".ajax-post").click(function () {
            var url = $(this).attr('url');

            ajaxCommon(url, "post");
        })

        $(".confirm").click(function () {
            var url = $("form").attr("url");
            var data = $("form").serializeArray();

            ajaxCommon(url, "post", data)
        })

        // var ids = $.cookie('select_ids');
        //
        // if (ids != "null"){
        //     ids = ids.split(",");
        // }else {
        //     ids = new Array();
        // }
        //
        // var check_boxes = $("input[name='ids']")
        //
        // // 选中cookie中保存的
        // if (ids instanceof Array == true && ids.length > 0){
        //     $.each(ids, function (index, item) {
        //         $.each(check_boxes, function (k, v) {
        //             if ($(v).val() == item){
        //                 $("input[name='ids'][value="+item+"]").attr('checked', true)
        //             }
        //         })
        //     })
        // }

        // 多选框改变事件
        $('.select_id').change(function () {
            var checkBox = $("input[name='ids']")
            var checkBoxSelected = $("input[name='ids']:checked")
            if (checkBox.length == checkBoxSelected.length) {
                $(".ids").prop('checked', true)
            } else {
                $(".ids").prop('checked', false)
            }
        })

        // 全选
        $(".ids").change(function () {
            if ($(this).is(":checked")){
                $("input[name='ids']").prop('checked', true)
            } else {
                $("input[name='ids']").prop('checked', false)
            }
        })

        // 选中启用
        $(".btn_enable").click(function () {
            var select_ids = $(".select_id:checked")
            if (select_ids.length == 0){
                layer.alert("请至少选择一个！",{
                    btn: ['确定'] //按钮
                });
                return;
            }
            var url = $(this).attr("url");
            var data = new Array();
            $.each(select_ids, function (index, item) {
                data.push($(item).val())
            })
            var endData = {
                "status": 1,
                "id": data.join(',')
            }

            ajaxCommon(url, "post", endData)
        })

        // 选中禁用
        $(".btn_disable").click(function () {
            var select_ids = $(".select_id:checked")
            if (select_ids.length == 0){
                layer.alert("请至少选择一个！",{
                    btn: ['确定'] //按钮
                });
                return;
            }
            var url = $(this).attr("url");
            var data = new Array();
            $.each(select_ids, function (index, item) {
                data.push($(item).val())
            })
            var endData = {
                "status": 0,
                "id": data.join(',')
            }

            ajaxCommon(url, "post", endData)
        })

        // 选中删除
        $(".btn_delete").click(function () {
            var select_ids = $(".select_id:checked")
            if (select_ids.length == 0){
                layer.alert("请至少选择一个！",{
                    btn: ['确定'] //按钮
                });
                return;
            }
            var url = $(this).attr("url");
            var data = new Array();
            $.each(select_ids, function (index, item) {
                data.push($(item).val())
            })
            var endData = {
                "id": data.join(',')
            }

            ajaxCommon(url, "post", endData)
        })

        $(".uploadImage").change(function () {
            var url = "{{url("Base/uploadImage")}}"
            var data = new FormData($("#form")[0]);

            $.ajax({
                url: url,
                type: "post",
                data: data,
                contentType: false,
                processData: false,
                success: function (res) {
                    if (res.status == 0){
                        layer.alert(res.message, {
                            btn: ["确定"]
                        })
                    } else {
                        $("#image_show").attr("src", res.url)
                    }
                }
            })
        })
    })

    // 上传图片
    function uploadImage() {
        $(".uploadImage").click()
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

    function ajaxCommon(url, method, data) {
        $.ajax({
            url: url,
            type: method,
            data: data,
            success: function (res) {
                if (res.status == 1){
                    layer.msg(res.message)
                    setTimeout(function () {
                        if (res.url != ""){
                            window.location.href = res.url
                        }
                    },1500)
                }else {
                    layer.alert(res.message, {
                        skin: 'layui-layer-lan',
                        closeBtn: 0
                    });
                }
            }
        })
    }
    var app = new Vue({
      el: '#app',
      data: {
          menus: [],
          childrenMenus: [],
          test: 1
      },
        created: function () {
            this.getMenus();
        },
        mounted: function(){
            this.getChildrenMenus()
        },
        updated: function(){
            this.getCurrentUrl();
        },
        methods:{
          getMenus: function () {
              var $app = this;
              var url = "{{ url('Base/menus') }}";
              $.ajax({
                  url: url,
                  type: "GET",
                  async : false,
                  success: function (result) {
                      $app.menus = result.info
                  }
              })
          },
          getCurrentUrl:function () {
              //先重置active
              $(".menus").removeClass('active');
             var currentUrl = window.location.pathname;
             var parentUrl = window.location.pathname;
             var getSecondMenusUrl = "{{url('Base/secondMenusUrl')}}";
             // 获取当前url的二级菜单
              $.ajax({
                  url: getSecondMenusUrl,
                  data: {
                      current_url: currentUrl
                  },
                  async: false,
                  type: "get",
                  success: function (res) {
                      currentUrl = res.info.second_url;
                      parentUrl = res.info.top_url;
                  }
              })
             if (currentUrl == "/"){
                 $(".menus[href='/User/list']").parent('li').addClass('active');
                 $(".childrenMenus[href='/User/list']").parent('li').addClass('active');
             }else {
                 $(".menus[href='"+parentUrl+"']").parent('li').addClass('active');
                 $(".childrenMenus[href='"+currentUrl+"']").parent('li').addClass('active');
             }
          },
          getChildrenMenus: function () {
              var $app = this;
                //获取当前路径
                var currentUrl = window.location.pathname;
                var parent_id = 0;
                //获取父菜单id
                if (currentUrl == "/"){
                    parent_id = $(".menus[href='/User/list']").attr('menu_id');
                }else {
                    var currentUrl = window.location.pathname;
                    var getSecondMenusUrl = "{{url('Base/secondMenusUrl')}}";
                    // 获取当前url的二级菜单
                    $.ajax({
                        url: getSecondMenusUrl,
                        data: {
                            current_url: currentUrl
                        },
                        async: false,
                        type: "get",
                        success: function (res) {
                            currentUrl = res.info.second_url;
                        }
                    })
                    parent_id = $(".menus[href='"+currentUrl+"']").attr('menu_id');
                }
                var url = "{{url('Base/childMenus')}}?parent_id=" + parent_id;
                $.ajax({
                    url: url,
                    type: "GET",
                    success: function (res) {
                        app.childrenMenus = res.info
                    }
                })
            }
        }
    })
</script>
</html>
@section('script')
@show