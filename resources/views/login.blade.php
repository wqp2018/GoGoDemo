<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <title>请登录</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">

    <script type="text/javascript" src="{{ asset('/js/jquery.min.js')  }}"></script>
    <script type="text/javascript" src="{{ asset('/js/layer.js')  }}"></script>
    <script type="text/javascript" src="{{ asset('js/vue.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap.js') }}"></script>
    <title>Document</title>
</head>
<style>
    * {
        box-sizing: border-box;
    }

    *:focus {
        outline: none;
    }
    body {
        font-family: Arial;
        background-color: #3498DB;
        padding: 50px;
    }
    .login {
        margin: 20px auto;
        width: 300px;
    }
    .login-screen {
        background-color: #FFF;
        padding: 20px;
        border-radius: 5px
    }

    .app-title {
        text-align: center;
        color: #777;
    }

    .login-form {
        text-align: center;
    }
    .control-group {
        margin-bottom: 10px;
    }

    input {
        text-align: center;
        background-color: #ECF0F1;
        border: 2px solid transparent;
        border-radius: 3px;
        font-size: 16px;
        font-weight: 200;
        padding: 10px 0;
        width: 250px;
        transition: border .5s;
    }

    input:focus {
        border: 2px solid #3498DB;
        box-shadow: none;
    }

    .btn {
        border: 2px solid transparent;
        background: #3498DB;
        color: #ffffff;
        font-size: 16px;
        line-height: 25px;
        padding: 10px 0;
        text-decoration: none;
        text-shadow: none;
        border-radius: 3px;
        box-shadow: none;
        transition: 0.25s;
        display: block;
        width: 250px;
        margin: 0 auto;
    }

    .btn:hover {
        background-color: #2980B9;
    }

    .login-link {
        font-size: 12px;
        color: #444;
        display: block;
        margin: auto;
        margin-top: 12px;
    }

</style>
<body>
<div class="login">
    <div class="login-screen">
        <div class="app-title">
            <h1>Login</h1>
        </div>
        <form method="post" @if(isset($admin)) url="{{url('/adminLogin')}}" @else url="{{url('/login')}}" @endif>
            <div class="login-form">
                <div class="control-group">
                    <input type="text" class="login-field" name="username" value="" placeholder="请输入用户名" id="login-name">
                    <label class="login-field-icon fui-user" for="login-name"></label>
                </div>

                <div class="control-group">
                    <input type="password" class="login-field" name="password" value="" placeholder="请输入密码" id="login-pass">
                    <label class="login-field-icon fui-lock" for="login-pass"></label>
                </div>

                <div class="control-group">
                    <input class="login-field" value="" name="captcha" placeholder="请输入验证码" id="login-captcha">
                    <img id="captcha" style="cursor: pointer;margin-top: 10px" onclick="changeCaptcha()" src="{{url('getCaptcha')}}">
                </div>

                <button type="button" class="confirm btn btn-primary">确定</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
<script>
    $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
    $(function () {
        $(".confirm").click(function () {
            var err = checkData();
            if (err != true){
                layer.msg(err);
                return;
            }
            var url = $("form").attr("url");
            var data = $("form").serializeArray();

            $.ajax({
                url: url,
                type: "post",
                data: data,
                success: function (res) {
                    if (res != null && res.code == 0){
                        layer.confirm(res.msg, {
                            btn: ['确定'] //按钮
                        });
                    }else {
                        layer.msg(res.msg);
                        setTimeout(function () {
                            window.location.href = "{{url('User/list')}}"
                        }, 1000)
                    }
                }
            })
        })
    })

    // 检查表单完整
    function checkData() {
        var username = $("#login-name").val();
        if (username == ""){
            return "用户名不能为空";
        }
        var password = $("#login-pass").val();
        if (password == ""){
            return "密码不能为空";
        }
        var captcha = $("#login-captcha").val();
        if (captcha == ""){
            return "请先输入验证码";
        }
        return true;
    }

    // 刷新验证码
    function changeCaptcha() {
        var url = "{{url('getCaptcha')."?date="}}" + new Date().getTime();

        $("#captcha").attr('src', url)
    }
</script>