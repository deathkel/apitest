<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>api</title>

    <link rel="stylesheet" href="/api/styles/amazeui.css">

    <link rel="stylesheet" href="/api/styles/main.css">
    <link rel="stylesheet" href="/api/styles/jsonFormater.css"/>

</head>
<body>


<header class="am-topbar iyz-topbar">
    <h1 class="am-topbar-brand">
        <a href="#">api</a>
    </h1>


</header>

<div class="iyz-lnav" id="iyz-lnav">
    @foreach($api as $key1=>$controller)
        <ul class="am-nav">
            <li>
                <a class="first-name" data-am-collapse="{parent: '#iyz-lnav', target: '#tag-{{$key1}}'}">
                    {{$controller["classname"]}}
                </a>
                <ul class="am-collapse am-list side-bar-right" id="tag-{{$key1}}">
                    @foreach($controller["method"] as $key2=>$action)
                        <li class="list">
                            <a class="list" onclick="getApiDetail({{$key1}},{{$key2}})">
                                <span class="am-point"></span>
                                {{$action['name']}}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>
        </ul>
    @endforeach

</div>

<div class="content">
    <ol class="am-breadcrumb am-breadcrumb-slash">
        <li><a href="#">api文档</a></li>
        <li class="am-active">api</li>

    </ol>
    <div class="am-u-sm-12 am-u-sm-centered">

        <form class="am-form am-form-horizontal J-form">
            <div class="J-comments"></div>
            <div class="J-method">
                <div class="am-form-group J-method">
                    <label class="am-u-sm-2 am-form-label a-method"></label>
                    <div class="am-u-sm-10">
                        <p class="am-margin-top-xs a-uri"></p>
                    </div>
                </div>

                <div class="am-form-group">
                    <label class="am-u-sm-2 am-form-label">参数</label>
                    <div class="am-u-sm-10 a-param">

                    </div>

                </div>


                <div class="am-form-group">
                    <div class="am-u-sm-10 am-u-sm-offset-2">
                        <button type="button" class="am-btn am-btn-default J-submit">提交</button>
                    </div>
                </div>
            </div>
        </form>

    </div>

    <hr/>
    <div class="am-u-sm-12 am-u-sm-centered">


        <p>返回值：</p>
        <div class="pre-code">

        </div>
    </div>

</div>

<script src="/api/js/jquery.js"></script>
<script src="/api/js/amazeui.js"></script>
<script src="/api/js/jsonFormater.js"></script>
<script>


    $(function () {
        window.getApiDetail = getApiDetail;

        var api = {!!json_encode($api)!!};

        var options = {
            dom: '.pre-code' //对应容器的css选择器
        };

        var jf = new JsonFormater(options); //创建对象


        function getApiDetail(x, y) {

            $('.J-comments').html('');//清空comments中的内容
            $('.J-method').show();//显示表单按钮
            var detail = api[x]['method'][y];
            var comment = api[x]['method'][y]['comment'];
            if (typeof comment == "string") {
                var newComment = comment.split('\n').join('<br>');
                $('.J-comments').append(newComment);
                $('.J-method').hide();//隐藏表单按钮
                return;
            }
            var method = detail['route']['method'];
            $('.a-method').html(method);

            var uri = detail['route']['uri'];

            //comment
            var htmlComment = '';
            if (comment) {
                $.each(comment, function (n, value) {
                    if (n == 'apiTest' || n == 'param') {
                        return;
                    }
                    htmlComment += '<div class="am-form-group">';
                    htmlComment += '<label class="am-u-sm-2 am-form-label">' + n + '</label>';
                    htmlComment += '<div class="am-u-sm-10">';
                    $.each(value, function (m, svalue) {
                        htmlComment += ' <p class="am-margin-top-xs">' + svalue + '</p>';
                    })
                    htmlComment += '</div>';
                    htmlComment += '</div>';
                })
            }

            $('.J-comments').append(htmlComment);


            var html = '<div class="J-params">';
            var param = detail['comment']['param'];
            console.log(param)
            if (param) {
                $.each(param, function (n, value) {

                    html += '<div class="am-g am-margin-bottom J-param">';
                    html += '<div class="am-u-sm-2 am-margin-top-xs">';
                    html += '</a><input class="p-key" type="text" value="' + value['name'] + '">';
                    html += '</div>';
                    html += '<div class="am-u-sm-1 am-margin-top-xs" style="width: 30px;">';
                    html += '<h1>=</h1>';
                    html += '</div>';
                    html += '<div class="am-u-sm-4 am-margin-top-xs">';
                    html += '<input class="p-value" type="text" placeholder="'+value['default']+'">';
                    html += '<a style="display: inline">'+value['type']+'</a>'
                    html += '</div>';
                    html += '<div class="am-u-sm-2 am-margin-top-xs">';
                    html += '<a href="javascript:" class="J-del"><i class="am-icon-close"></i></a>';
                    html += '</div>';
                    html += '</div>';

                });
            }
            html += '</div>'

            html += '<button type="button" class="am-btn am-btn-primary am-btn-xs J-add">新增一行</button>';

            $('.a-param').html(html);
            var uris = uri.split('/');

            console.log(uri);
            var html2 = '';
            uris.forEach(function (ele, index, array) {
                var reg = /{/;
                if (reg.test(ele)) {
                    html2 += '<input class="uri-static" placeholder="' + ele + '"/>'
                } else {
                    html2 += '<span class="uri-static">' + ele + '</span>';
                }
                if (index < array.length - 1) {
                    html2 += '/'
                }

            });

            $('.a-uri').html(html2)


        }

        $('.J-submit').click(function () {
            console.log('123')
            var method = $('.a-method').html();
            var j_param = $('.a-param').find('.J-param');
            var uri_static = $('.a-uri').find('.uri-static');
            var data = {};
            var url = '';

            j_param.each(function () {
                var p_key = $(this).find('.p-key').val();
                var p_value = $(this).find('.p-value').val();
                data[p_key] = p_value;
            })

            var flag = 0;
            uri_static.each(function (index) {
                var nodename = $(this).context.nodeName;
                if (nodename == "SPAN") {
                    url += $(this).html();
                } else if (nodename == "INPUT") {
                    if ($(this).val()) {
                        url += $(this).val();
                    } else {
                        flag = 1;

                    }


                }

                if (index < uri_static.length - 1) {
                    url += '/'
                }

            })

            if (flag == 1) {
                alert("请填写完整uri");

                return;
            }

            console.log(url)
            $.ajax({
                type: method,
                url: url,
                data: data,
                dataType: "json",
                success: function (data) {
                    jf.doFormat(JSON.stringify(data)); //格式化json
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    jf.doFormat(JSON.stringify("status:" + xhr.status + "  thrownError:" + thrownError))
                }
            });
        });

        //删除一行param
        $('.a-param').on('click', '.J-del', function () {
            $(this).parents('.J-param').remove();
        });

        //新增一行param
        $('.a-param').on('click', '.J-add', function () {

            var html = '';
            html += '<div class="am-g am-margin-bottom J-param">';
            html += '<div class="am-u-sm-2 am-margin-top-xs">';
            html += '<input class="p-key" type="text" value="">';
            html += '</div>';
            html += '<div class="am-u-sm-1 am-margin-top-xs" style="width: 30px;">';
            html += '<h1>=</h1>';
            html += '</div>';
            html += '<div class="am-u-sm-4 am-margin-top-xs">';
            html += '<input class="p-value" type="text" placeholder="参数">';
            html += '</div>';
            html += '<div class="am-u-sm-2 am-margin-top-xs">';
            html += '<a href="javascript:" class="J-del"><i class="am-icon-close"></i></a>';
            html += '</div>';
            html += '</div>';
            $('.J-params').append(html);


        });
    })
</script>
</body>
</html>
