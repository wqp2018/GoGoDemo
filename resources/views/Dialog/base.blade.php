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
<body>
@section('body')
@show
</body>
</html>
@section('script')
    @show