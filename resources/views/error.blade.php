<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>错误提示</title>
    <script type="text/javascript" src="{{ asset('/js/jquery.min.js')  }}"></script>
    <script type="text/javascript" src="{{ asset('/js/layer.js')  }}"></script>
    <script type="text/javascript" src="{{ asset('/js/bootstrap.js') }}"></script>
</head>
<body>

</body>
<script>
    $(function () {
        layer.alert("{{$msg}}！",{
            btn: ['确定'],
            yes:function () {
                window.history.go(-1)
            }
        });

    })
</script>
</html>