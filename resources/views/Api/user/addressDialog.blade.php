@extends('Dialog.base')

@section('style')
    <style>

    </style>
    @stop

@section('body')
    <div class="container">
        <div class="list-group">
            @foreach($address_list as $k => $v)
                <div style="height: 80px; line-height: 60px; cursor: pointer" class="list-group-item" onclick="selectAddress({{$v['id']}})">
                    {{$v['address']}} &nbsp;&nbsp;&nbsp;&nbsp; {{$v['linkman']}} &nbsp;&nbsp;&nbsp;&nbsp; {{$v['phone']}}
                    <a href="javascript:void(0)" class="editAddress" style="display: inline-block; position: relative; left: 40%"><span class="glyphicon glyphicon-pencil"></span></a>
                    @if($v['id'] == $select_address_id)
                        <span class="glyphicon glyphicon-ok"></span>
                        @endif
                </div>
                @endforeach

        </div>
    </div>
    @stop

@section('script')
    <script>
        function selectAddress(id) {
            var url = "{{url('OrderApi/chooseAddress')}}?address_id=" + id
            $.ajax({
                url: url,
                success: function (res) {
                    var index=parent.layer.getFrameIndex(window.name);
                    parent.layer.close(index);

                    parent.refreshBeforeOrdering(res.data)
                }
            })

        }

        $(function () {
            $(".editAddress").click(function (e) {

                e.stopPropagation();
            })
        })
    </script>
    @stop