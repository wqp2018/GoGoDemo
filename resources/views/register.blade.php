<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <title>注册账号</title>
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
        background-color: #d0d0d0;
        padding: 50px;
    }
    .register {
        margin: 20px auto;
        width: 300px;
    }
    .register-screen {
        background-color: #FFF;
        padding: 20px;
        border-radius: 5px
    }

    .app-title {
        text-align: center;
        color: #777;
    }

    .register-form {
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

    .register-link {
        font-size: 12px;
        color: #444;
        display: block;
        margin: auto;
        margin-top: 12px;
    }

</style>
<body>
<div class="register">
    <div class="register-screen">
        <div class="app-title">
            <h1>注册GoGo</h1>
        </div>
        <form method="post" @if(isset($admin)) url="{{url('/register')}}" @else url="{{url('/register')}}" @endif>
            <div class="register-form">
                <div class="control-group">
                    <input type="text" class="register-field" name="user_name" value="" placeholder="请输入用户名" id="register-name">
                    <label class="register-field-icon fui-user" for="register-name"></label>
                </div>

                <div class="control-group">
                    <input type="password" class="register-field" name="password_encry" value="" placeholder="请输入密码" id="register-pass">
                    <label class="register-field-icon fui-lock" for="register-pass"></label>
                </div>
                
                <div class="control-group">
                    <input type="email" class="register-field" name="email" value="" placeholder="请输入邮箱" id="register-email">
                    <label class="register-field-icon fui-lock" for="register-email"></label>
                </div>

                <div class="control-group">
                    <input class="register-field" value="" name="validate_code" placeholder="请输入邮箱验证码" id="register-validateCode">
                </div>

                <div style="margin-bottom: 10px">
                    <button onclick="sendEmailValidate(this)" type="button" style="width: 120px;display: inline-block" class="btn btn-primary">发送验证码</button>
                    <button type="button" style="width: 120px;display: inline-block" disabled="disabled" class="confirm btn btn-primary btn-confirm">确认信息</button>
                </div>
                <a href="{{url('/login')}}">返回登录</a>
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
                    console.log(res)
                    if (res == "" || res.code == 0){
                        layer.confirm(res.msg, {
                            btn: ['确定'] //按钮
                        });
                    }else {
                        layer.msg(res.msg);
                        setTimeout(function () {
                            window.location.href = res.url
                        }, 1000)
                    }
                }
            })
        })
    })

    // 检查表单完整
    function checkData() {
        var username = $("#register-name").val();
        if (username == ""){
            return "用户名不能为空";
        }
        var password = $("#register-pass").val();
        if (password == ""){
            return "密码不能为空";
        }
        var email = $("#register-email").val();
        if (email == ""){
            return "邮箱不能为空";
        }
        var validateCode = $("#register-validateCode").val();
        if (validateCode == ""){
            return "请先输入验证码";
        }
        return true;
    }

    // 发送邮件验证码
    function sendEmailValidate(_this) {
        var url = "{{url('/register/sendEmail')}}";
        var email = $("#register-email").val();
        if (email == ""){
            layer.alert("请先输入邮箱账号!",{
                btn: ['确定']
            })
            return
        }
        var data = {
            email: email
        }

        $(_this).prop("disabled", "disabled")
        $.ajax({
            url: url,
            type: "post",
            data: data,
            success: function (res) {
                if (res.code == 0){
                    layer.alert(res.msg,{
                        btn: ['确定']
                    })
                } else {
                    layer.msg(res.msg);
                    $(".btn-confirm").removeAttr("disabled")
                    var time = 60;
                    var timer = setInterval(function () {
                        $(_this).text("重新发送(" + time + ")")
                        time--;
                        if (time < 0){
                            clearInterval(timer);
                            $(_this).text("重新发送");
                            $(_this).removeAttr("disabled");
                        }
                    },1000)
                }
            }
        })
    }
</script>