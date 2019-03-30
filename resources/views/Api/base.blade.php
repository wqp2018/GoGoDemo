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
    @section('style')
    @show

    <title>Document</title>
</head>
<body>
@section('body')
    @show
</body>
<script>
    $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
    $(function () {
        $(".confirm").click(function () {
            var url = $("#form").attr('url');
            var data = $("form").serializeArray();

            $.ajax({
                url: url,
                data: data,
                type: "post",
                success: function (res) {
                    if (res.status == 1){
                        layer.msg(res.msg)
                        setTimeout(function () {
                            if (res.url != ""){
                                window.location.href = res.url
                            }
                        },1500)
                    }else {
                        layer.alert(res.msg, {
                            skin: 'layui-layer-lan',
                            closeBtn: 0
                        });
                    }
                }
            })
        })
    })

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
</script>
</html>
@section('script')
    @show