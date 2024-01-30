<!DOCTYPE html>
<html class="light" id="adminHtml">
<head>
    <meta charset="UTF-8"/>
    <title>@yield('title') - Shopwwi智能管理系统</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>
    <meta name="author" content="Shopwwi"/>
    <meta name="copyright" content="Shopwwi Inc. All Rights Reserved"/>
    <meta http-equiv="X-UA-Compatible" content="IE=Edge"/>
    @yield('css')
    <script src="/static/unpkg/vue/dist/vue.global.js"></script>
    <link rel="stylesheet" href="/static/unpkg/tdesign-vue-next/dist/tdesign.min.css" />
    <script src="/static/unpkg/tdesign-vue-next/dist/tdesign.min.js"></script>
    <link rel="stylesheet" href="/static/sdk/helper.css" />
    <link rel="stylesheet" href="/static/sdk/iconfont.css" />
    <link rel="stylesheet" href="/static/sdk/sdk.css" />
    <link rel="stylesheet" href="/static/css/admin.css"/>
    <link rel="stylesheet" href="/static/fonts/remixicon.css"/>
    <style>
        html, body,.app-wrapper{
            position: relative;
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
        }
        .amis-scope{ background: transparent}
        .amis-scope .cxd-Page-headerRow{ border-bottom: none; background-color: var(--colors-neutral-fill-10)}
        .amis-scope .cxd-Page-header{padding-left: 0; background-color: var(--colors-neutral-fill-10)}
        .amis-scope .border-0{border: 0}
        .amis-scope .cxd-Page-main .border-b-0 {border-bottom-width: 0px;}
        .amis-scope .box-shadow-none{box-shadow: none}
        .amis-scope .bg-transparent,.amis-scope .cxd-Page-main .bg-transparent{ background: transparent}
        .amis-scope .cxd-Page-main .p-0{padding: 0}
        .amis-scope .cxd-Page-body{ background: var(--body-bg)}
        .hd-border-b{    border-bottom: var(--borderWidth) solid var(--borderColor);}
        i{line-height: 1;font-size: 1rem;}
    </style>
    <script>
        var shopwwiAdminUrl = "{{ shopwwiAdminUrl('') }}",shopwwiAdminPrefix = "{{ config('plugin.shopwwi.admin.app.prefix.admin','admin') }}",Editor;
    </script>
</head>
<body id="fullscreen">
    <div id="vueApp" class="app-wrapper overflow-hidden">
        <wwi-layout>@yield('content')</wwi-layout>
    </div>
</body>
<script src="/static/unpkg/vuedraggable/sortable.js"></script>
<script src="/static/unpkg/vuedraggable/dist/vuedraggable.umd.min.js"></script>
<script src="/static/js/common.js"></script>
<script src="/static/js/admin.js"></script>

@include('admin.layouts.components.layout')
<script>
    const { createApp } = Vue;
    let vueComponents = {},vueData = {},vueMethods = {},vueAppend = {};
</script>
@yield('js')
<script>
    const wwiApp = createApp({
        components:{ WwiLayout,...vueComponents },
        data(){
            return vueData
        },
        ...vueAppend,
        methods:vueMethods
    }).use(TDesign);
</script>
@yield('js2')
<script> wwiApp.mount('#vueApp') </script>
</html>