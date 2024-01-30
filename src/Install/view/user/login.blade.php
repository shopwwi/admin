<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8" />
    <title>{{ $title ?? 'Shopwwi智能管理系统' }}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>
    <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
    <link rel="stylesheet" href="/static/sdk/sdk.css" />
    <link rel="stylesheet" href="/static/fonts/remixicon.css"/>
    <link rel="stylesheet" href="/static/css/theme.css" />
    <link rel="stylesheet" href="/static/css/user.css" />
    <style>
        html,
        body,
        .app-wrapper {
            position: relative;
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            background-color: #f8f8f8;
        }
        .amis-scope,.amis-scope .cxd-Page{ background: transparent}
    </style>
</head>
<body class="user-login">
<div id="root" class="app-wrapper"></div>
<script src="/static/sdk/sdk.js"></script>
<script type="text/javascript">
    (function () {
        let amis = amisRequire('amis/embed');
        // 通过替换下面这个配置来生成不同页面
        let amisJSON =  @json($json??[]);
        let amisScoped = amis.embed('#root', amisJSON);
    })();
</script>
<link href="/static/sdk/helper.css" rel="stylesheet" type="text/css" />
</body>
</html>