<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8" />
    <title>{{ $title ?? 'Shopwwi智能管理系统' }}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta
            name="viewport"
            content="width=device-width, initial-scale=1, maximum-scale=1"
    />
    <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
    <link rel="stylesheet" href="/static/sdk/sdk.css" />
    <link rel="stylesheet" href="/static/sdk/helper.css" />
    <link rel="stylesheet" href="/static/sdk/iconfont.css" />
    <link rel="stylesheet" href="/static/css/admin.css" />
    <link rel="stylesheet" href="/static/fonts/remixicon.css"/>
    <!-- 这是默认主题所需的，如果是其他主题则不需要 -->
    <!-- 从 1.1.0 开始 sdk.css 将不支持 IE 11，如果要支持 IE11 请引用这个 css，并把前面那个删了 -->
    <!-- <link rel="stylesheet" href="sdk-ie11.css" /> -->
    <!-- 不过 amis 开发团队几乎没测试过 IE 11 下的效果，所以可能有细节功能用不了，如果发现请报 issue -->
    <style>
        html,
        body,
        .app-wrapper {
            position: relative;
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
        }
        .login-box{ max-width: 450px; width: 100%}
        .login-box .login-input{border-radius: 9999px!important;  height: 45px}
        .login-box .login-input .cxd-TextControl-inputPrefix{
            font-family: 'remixicon' !important;
            font-size: 18px;
            font-style: normal;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            opacity: 0.5;
            margin-right: 0.25rem;
        }
        .login-box .login-input.user .cxd-TextControl-inputPrefix:before{content: '\ea09';}
        .login-box .login-input.password .cxd-TextControl-inputPrefix:before{content: '\eece';}
        .login-box .login-input.code .cxd-TextControl-inputPrefix:before{content: '\f107';}

        .amis-scope,.amis-scope .cxd-Page{ background: transparent}
        .amis-scope .bg-transparent,.amis-scope .cxd-Page-main .bg-transparent{ background: transparent}
    </style>
</head>
<body class="admin-login">
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
</body>
</html>