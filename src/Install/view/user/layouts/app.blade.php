<!DOCTYPE html>
<html class="light" id="adminHtml">
<head>
    <meta charset="UTF-8"/>
    <title>@yield('title') - 会员中心 - {{shopwwiConfig('siteInfo.siteName','Shopwwi智能管理系统')}}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>
    <meta http-equiv="X-UA-Compatible" content="IE=Edge"/>
    <meta name="author" content="Shopwwi"/>
    <meta name="copyright" content="Shopwwi Inc. All Rights Reserved"/>
    @yield('css')
    <script src="/static/unpkg/vue/dist/vue.global.js"></script>
    <link rel="stylesheet" href="/static/unpkg/tdesign-vue-next/dist/tdesign.min.css" />
    <script src="/static/unpkg/tdesign-vue-next/dist/tdesign.min.js"></script>
    <link rel="stylesheet" href="/static/sdk/sdk.css" />
    <link rel="stylesheet" href="/static/sdk/helper.css" />
    <link rel="stylesheet" href="/static/sdk/iconfont.css" />
    <link rel="stylesheet" href="/static/css/theme.css" />
    <link rel="stylesheet" href="/static/fonts/remixicon.css"/>
    <link rel="stylesheet" href="/static/css/user.css" />
    <style>
        html, body,.app-wrapper{
            position: relative;
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
        }
        .amis-scope{background-color: var(--colors-neutral-fill-10); overflow: auto}
    </style>
    <script>
        var shopwwiMallUrl = "{{ shopwwiMallUrl('') }}",shopwwiSiteUrl = "{{ shopwwiMallUrl('') }}",shopwwiUserUrl = "{{ shopwwiUserUrl('') }}";
    </script>
</head>
<body>
<div id="vueApp" class="amis-scope">
    <div class="top-layout">
        <div class="wrap m:wrap flex items-center justify-between">
            <div>HI {{ $userInfo->nickname ?? '游客' }} 欢迎您<a href="{{ shopwwiUserUrl('auth/logout') }}" class="ml-2">退出</a></div>
            <div class="">
                <a href="/">返回首页</a>
            </div>
        </div>
    </div>
    <div class="header">
        <div class="wrap py-4 flex m:wrap">
            <div class="flex items-center flex-shrink-0">
                <a href="{{shopwwiUserUrl('')}}" class="logo"><img src="{{shopwwiConfig('siteInfo.siteLogo','')}}" class="w-full h-full" /></a>
                <h1 class="">会员中心</h1>
            </div>
            <div class="flex-1 flex mx-4 items-center">
                <div class="nav-link"><a href="">首页</a></div>
                <div class="nav-link"><a href="">账户设置</a></div>
                <div class="nav-link"><a href="">商城</a></div>
            </div>
            <div class="flex items-center flex-shrink-0">
                <div class="cxd-Badge">
                    <div class="text-light bg-primary rounded-full w-6 h-6 flex items-center justify-center"><i class="ri-notification-2-fill"></i> </div>
                    <span class="cxd-Badge-text cxd-Badge--top-right cxd-Badge--danger" style="border-radius: 8px; height: 18px; line-height: 16px;">25</span>
                </div>
                <a href="{{shopwwiUserUrl('message')}}" class="ml-2">消息</a>
            </div>
        </div>
    </div>
<div class="flex wrap m:wrap mt-4">
    @section('sidebar')
        <div class="user-nav" :class="{'is-close-nav':domBind.menuShow}">
            <div class="replace h-full overflow-y-auto">
                <ul class="cxd-AsideNav-list">
                    @foreach($userMenus as $item)
                        @include('user.layouts.menu',['item'=>$item])
                    @endforeach
                </ul>
                <div class="user-nav-back" @click="domBind.menuShow =! domBind.menuShow"><i class="ri-arrow-left-double-line"></i></div>
            </div>

        </div>
    @show
        <div class="overflow-hidden box-border flex-1 pc:ml-4">
            @yield('content')
        </div>
</div>
    <div class="wrap m:wrap text-center py-4">Copyright © <?php echo date('Y');?> All rights reserved. {{shopwwiConfig('siteInfo.siteName','Shopwwi智能管理系统')}} <a href="https://beian.miit.gov.cn/" target="_blank">{{shopwwiConfig('siteInfo.siteIcp','')}}</a> </div>
</div>
</body>
<script src="/static/js/common.js"></script>
<script src="/static/js/user.js"></script>
<script>
    const { createApp } = Vue;
    let vueComponents = {},vueData = {},vueMethods = {},vueAppend = {};
</script>
@yield('js')
<script>
    useUtilsTabs('.user-nav .cxd-AsideNav-item','is-open');
    const wwiApp = createApp({
        components:{...vueComponents },
        data(){
            return {
                domBind:{},
                menuList:@json($userMenus),
                ...vueData
            }
        },
        mounted(){
            const activeMenu = '{{$activeKey??''}}';
            useUtilsTabs('.user-nav .cxd-AsideNav-item','is-open');
        },
        ...vueAppend,
        methods: {
            handleSelectMenu(dom,key){
                const className = 'is-open';
                console.log(dom.target)
                if(dom.target.parentNode.classList.contains(className)){
                    dom.target.parentNode.classList.remove(className);
                }else{
                    dom.target.parentNode.classList.add(className);
                }

            },
            ...vueMethods
        }
    }).use(TDesign);
</script>
@yield('js2')
<script> wwiApp.mount('#vueApp') </script>

</html>