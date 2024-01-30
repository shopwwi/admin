<template id="WwiLayout">
    <t-layout class="h-full">
        <t-aside class="flex-shrink-0 overflow-hidden" :width="collapsed ?'0px': 'auto'">
            <div class="flex h-full">
                <div class="wwi-layout-menuA flex-shrink-0">
                    <div class="logo"><img src="/static/uploads/common/logo-icon.png" style="object-fit: contain;"/></div>
                    <div id="leftMenu" class="menu-box">
                        <div class="flex flex-col text-center menu-item" v-for="item in menuList" @click="handleMenuA(item)" :class="{'menu-item-active':oneActive === item.id}">
                            <i :class="item.icon" v-if="item.icon"></i>
                            <span class="font-bold text-md ">@{{ item.name }}</span>
                        </div>
                    </div>
                </div>
                <div class="flex-1 wwi-layout-menuB" v-if="menuLevelList.length > 0">
                    <div class="title p-4"><img src="/static/uploads/common/logo.png" class="w-full h-full" style="object-fit: contain;"/></div>
                    <div style="height:calc(100vh - var(--td-comp-size-xxxl))">
                        <t-menu
                                theme="light"
                                :default-expanded = "[twoActive,threeActive]"
                                v-model:value="nowActive"
                                expand-mutex width="100%"
                        >
                            <template v-for="(item,index) in menuLevelList" :key="item.id">
                                <t-submenu :value="item.id" :title="item.name" v-if="item.children && item.children.length > 0">
                                    <t-menu-item style="padding-left:2rem" :value="item2.id" :router="{}" v-for="item2 in item.children" :key="item2.id" @click="handleMenuItem(item2,3)">@{{ item2.name }}</t-menu-item>
                                </t-submenu>
                                <t-menu-item :value="item.id" :key="item.id" :router="{}" @click="handleMenuItem(item,2)" v-else>@{{ item.name }}</t-menu-item>
                            </template>

                        </t-menu>
                    </div>
                </div>
            </div>
        </t-aside>
        <t-layout class="overflow-auto">
            <t-header>
                <t-head-menu theme="light" v-model="forActive">
                    <template #logo>
                        <div class="header-operate-left">
                            <t-button theme="default" shape="square" variant="text" @click="collapsed = !collapsed">
                                <t-icon class="collapsed-icon" name="view-list" />
                            </t-button>
                        </div>
                    </template>

                    <t-menu-item :value="item.id" @click="handleMenuItem(item,4)" :router="{}" v-for="(item,index) in menuForList" :key="item.id">@{{ item.name }}</t-menu-item>
                    <template #operations>
                        <t-tooltip placement="bottom" content="访问首页">
                            <t-button theme="default" shape="square" variant="text" href="{{ shopwwiUserUrl('') }}" target="_blank">
                                <t-icon name="home" />
                            </t-button>
                        </t-tooltip>
                        <t-tooltip placement="bottom" content="放大">
                            <t-button theme="default" shape="square" variant="text" @click="toggleFullScreen">
                                <t-icon :name="fullIcon" />
                            </t-button>
                        </t-tooltip>
                        <t-tooltip placement="bottom" content="主题切换">
                            <t-button theme="default" shape="square" variant="text" @click="handleTheme">
                                <t-icon :name="themeIcon" />
                            </t-button>
                        </t-tooltip>
                        <t-dropdown :min-column-width="120" trigger="click">
                            <template #dropdown>
                                <t-dropdown-menu>
                                    <t-dropdown-item class="operations-dropdown-container-item" @click="handleNav('{{ shopwwiAdminUrl('index/info') }}')">
                                        <t-icon name="user-circle" class="mr-2"></t-icon>个人中心
                                    </t-dropdown-item>
                                    <t-dropdown-item class="operations-dropdown-container-item" @click="handleNav('{{ shopwwiAdminUrl('system/cache') }}')">
                                        <t-icon name="delete-time" class="mr-2"></t-icon>清空缓存
                                    </t-dropdown-item>
                                    <t-dropdown-item class="operations-dropdown-container-item" @click="handleNav('{{ shopwwiAdminUrl('auth/logout') }}')">
                                        <t-icon name="poweroff" class="mr-2"></t-icon>退出登录
                                    </t-dropdown-item>
                                </t-dropdown-menu>
                            </template>
                            <t-button class="header-user-btn" theme="default" variant="text">
                                <template #icon>
                                    <t-icon class="header-user-avatar" name="user-circle" />
                                </template>
                                <div class="header-user-account">{{ $adminInfo->username }}</div>
                                <template #suffix><t-icon name="chevron-down" /></template>
                            </t-button>
                        </t-dropdown>
                    </template>
                </t-head-menu>
            </t-header>
            <t-content>
                <t-layout class="wwi-layout">

                    <t-content>
                        <slot></slot>
                    </t-content>
                    <t-footer>Shopwwi智能管理系统</t-footer>
                </t-layout>
            </t-content>
        </t-layout>
    </t-layout>
</template>
<script>
    const WwiLayout = {
        name: 'WwiLayout',
        template: '#WwiLayout',
        data(){
            return{
                menuList: @json($adminMenus ?? []),
                activeMenu:'{{$activeKey ?? ''}}',
                oneActive: 'index',
                twoActive: '',
                threeActive:'',
                forActive: '',
                nowActive: '',
                fullIcon: 'fullscreen-1',
                themeIcon: 'sunny',
                collapsed : false,
                clickItem: null,
                expanded:[]
            }
        },
        mounted(){
            const openKeys = useUtilsConvertTreeData(useUtilsTopTreeNodes(JSON.parse(JSON.stringify(this.menuList)), 'id', this.activeMenu));
            if(openKeys && openKeys.length > 0){
                if (openKeys.length > 1) {
                    this.twoActive = openKeys[1].id;
                    this.nowActive = openKeys[1].id;
                }
                if (openKeys.length > 2) {
                    this.threeActive = openKeys[2].id;
                    this.nowActive = openKeys[2].id;
                }
                if (openKeys.length > 3) {
                    this.forActive = openKeys[3].id;
                }
                this.oneActive = openKeys[0].id;
            }
            document.addEventListener('fullscreenchange', this.toggleFullscreenIcon);
        },
        computed:{
            menuLevelList(){
                let menus = [];
                if (this.menuList.length > 0) {
                    this.menuList.forEach(item => {
                        if (this.oneActive === item.id && (item.children && item.children.length > 0)) {
                            menus = item.children;
                        }
                    })
                }
                return menus;
            },
            menuForList(){
                let menus = [];
                if (this.menuLevelList.length > 0) {
                    this.menuLevelList.forEach(item => {
                        if (item.children && item.children.length > 0) {
                            item.children.forEach(item2=>{
                                if (this.threeActive === item2.id && (item2.children && item2.children.length > 0)) {
                                    menus = item2.children.filter((item3)=> item3.visible === '1')
                                }
                            })
                        }
                    })
                }
                return menus;
            }
        },
        methods:{
            /**
             * 切换菜单
             * @param item
             */
            handleMenuA(item){
                if(this.menuAKey === item.id){
                    return;
                }
                this.oneActive = item.id;
                if(item.children && item.children.length > 0){
                    return ;
                }
                return this.handleMenuItem(item);
            },
            /**
             * 点击菜单切换
             * @param item
             * @param step
             */
            handleMenuItem(item,step = 1){
                if(item.menu_type === 'M'){
                    if(item.children && item.children.length > 0){
                        const menus = item.children.filter(item2 => {
                            return item2.menu_type === 'C' && item2.visible === '1'
                        });
                        if(menus.length > 0){
                            if(step === 3){
                                this.threeActive = item.id;
                            }
                            if(menus[0].is_frame == 1) return window.open(shopwwiAdminUrl + menus[0].path);
                            return this.handleVueRouter(menus[0],step + 1);
                        }
                    }
                    return;
                }
                if(item.is_frame == 1) return window.open(shopwwiAdminUrl + item.path);

                return this.handleVueRouter(item,step);

            },
            handleVueRouter(item,step){
                if(step === 2) this.twoActive = item.id;
                if(step === 3) this.threeActive = item.id;
               if(step === 4) this.forActive = item.id;
                if(this.$router && item.is_cache ==1){
                    const prefix = '/' + shopwwiAdminPrefix;
                    return this.$router.push(prefix + item.path);
                }
                return window.location.href = shopwwiAdminUrl + item.path;
            },
            handleNav(url) {
                window.location.href = url;
            },
            toggleFullScreen(){
                if (!document.fullscreenElement) {
                    document.getElementById("fullscreen").requestFullscreen();
                } else {
                    if (document.exitFullscreen) {
                        document.exitFullscreen();
                    }
                }
            },
            toggleFullscreenIcon(){
                if(document.fullscreenElement !== null){
                    this.fullIcon = 'fullscreen-exit-1';
                }else{
                    this.fullIcon = 'fullscreen-1';
                }
            },
            handleTheme(){
                const theme = localStorage.getItem('shopwwiAdminTheme');

                if(theme === 'dark'){
                    this.themeIcon = 'sunny';
                    console.log(theme)
                    localStorage.removeItem('shopwwiAdminTheme');
                    document.documentElement.removeAttribute('theme-mode');
                }else{
                    localStorage.setItem('shopwwiAdminTheme','dark')
                    this.themeIcon = 'moon';
                    document.documentElement.setAttribute('theme-mode', 'dark');
                }

            }
        }
    };
</script>