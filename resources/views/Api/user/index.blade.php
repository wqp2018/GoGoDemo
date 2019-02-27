<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <title>个人主页</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">

    <script type="text/javascript" src="{{ asset('/js/jquery.min.js')  }}"></script>
    <script type="text/javascript" src="{{ asset('/js/layer.js')  }}"></script>
    <script type="text/javascript" src="{{ asset('js/vue.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap.js') }}"></script>
    <title>Document</title>
</head>
<style>
    body{
        background: skyblue;
    }
    .head-background{
        background: url('/uploads/images/background.jpg') no-repeat center;
        background-size: 100%;
        height: 400px;
    }
    .store-list{
        margin-top: 20px;
    }
    .link-head{
        width:  100%;
        height: 40px;
        font-size: 18px;
        line-height: 40px;
        background: white;
    }
</style>
<body>
    <div class="container">
        <div class="user-info">
            <div class="head-background">
                <div style="text-align: center;padding-top: 60px">
                    <img src="{{$user['avatar']}}" style="border-radius: 50%;" height="120px" width="120px"><br />
                    <span style="font-size: 18px">{{$user['user_name']}}</span>
                    <div class="link-list" style="margin-top: 18px">
                        <ul class="nav" role="tablist">
                            <li role="presentation" class="active"><a href="#">个人信息</a></li>
                            <li role="presentation"><a href="#">我的订单</a></li>
                            <li role="presentation"><a href="#">信息</a></li>
                            <li role="presentation"><a href="#">退出登录</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="store-list">
                <div class="hot-store">
                    <div class="link-head">
                        <div style="display: inline-block; padding-left: 20px"><span class="glyphicon glyphicon-fire"></span> 热门餐厅</div>
                        <div style="display: inline-block; position: absolute; left: 78%"><a>更多 >></a></div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 col-md-3">
                            <div class="thumbnail">
                                <img src="{{'/uploads/images/default.jpg'}}" height="200px" width="200px">
                                <div class="caption">
                                    <h4>Thumbnail label</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<script>

</script>