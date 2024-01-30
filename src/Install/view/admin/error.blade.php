<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8" />
    <title>错误异常</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta
            name="viewport"
            content="width=device-width, initial-scale=1, maximum-scale=1"
    />
    <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
    <style>
        html,
        body{
            position: relative;
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            background-color: #f8f8f8;
            display: flex;
            align-items: center;
        }
        .err-box{
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
            background-color: #ffffff;
            min-height: 150px;
            padding: 1rem;
            border-radius: 1rem;
            box-shadow: 0 0 10px 3px #dce1e1;
        }
        .err-box h4,.err-box h1{color: #013477;text-align: center}
        .err-box h5{ color: #cbcbcb;text-align: center }
        .err-box .btns{padding: 1rem; text-align: center}
        .err-box .btns a{ padding: 0.5rem 1rem; background-color: #013477; color: #fff; text-decoration: none; font-weight: bold;font-size: 0.8rem;border-radius: 1rem;}
        .err-box .btns a:hover{ background-color:#2c4ba3}
    </style>
</head>
<body>
    @php
        $code = $code ?? 404;
        $url = $url ?? '';
        $msg = $msg ?? '';
        switch ($code){
            case 401:
                $msg = '请先完成登入后在操作';
                $url = [['url'=>shopwwiAdminUrl('auth/login'),'msg'=>'立即登入'],['url'=>'javascript:history.back()','msg'=>'返回上一页']];
                break;
            case 402:
                $msg = '登入超时，请重新登入';
                $url = [['url'=>shopwwiAdminUrl('auth/login'),'msg'=>'重新登入'],['url'=>'javascript:history.back()','msg'=>'返回上一页']];
                break;
            case 403:
                $msg = '无权限访问，请返回重试';
                $url = '';
                break;
        }
     @endphp
<div class="err-box">
    <h4>温馨提示</h4>
    <h1>{{ $msg }}</h1>
    <div class="btns">
        @if(isset($url) && is_array($url))
            @foreach($url as $v)
            <a href="{{ $v['url'] }}">{{ $v['msg'] }}</a>
            @endforeach
                <h5>无操作将在6S后自动跳转</h5>
            <script type="text/javascript"> window.setTimeout("javascript:location.href='{{ $url[0]['url'] }}'", {{ $time ?? 3600 }}); </script>
        @elseif(isset($url) && !empty($url))
            <a href="{{ $url }}">返回上一页</a>
            <h5>无操作将在6S后自动跳转</h5>
            <script type="text/javascript"> window.setTimeout("javascript:location.href='{{ $url }}'", {{ $time ?? 3600 }}); </script>
        @else
            <a href="javascript:history.back()">返回上一页</a>
            <h5>无操作将在6S后自动跳转</h5>
            <script type="text/javascript"> window.setTimeout("javascript:history.back()", {{ $time ?? 3600 }}); </script>
        @endif
    </div>
    <h5>©Shopwwi智能管理系统</h5>
</div>
</body>
</html>