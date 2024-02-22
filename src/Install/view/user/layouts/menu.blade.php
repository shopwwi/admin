<template id="WwiUserMenu">
<div class="user-nav" :class="{'is-close-nav':menuShow}">
    <div class="user-nav-lt">
        <div class="item" v-for="item in menuList" @click="handleMenuA(item)" :class="{'curr':oneActive === item.key}">@{{ item.name }}</div>
    </div>
    <div class="user-nav-rt replace h-full overflow-y-auto">
        <template v-for="(item,index) in menuLevelList" :key="item.id">
        <div class="sub-menu" v-if="item.children && item.children.length > 0">
            <div class="menu-item-sub">@{{ item.name }}</div>
            <template v-for="(item2,index) in item.children" :key="item2.id">
                <div class="sub-menu" v-if="item2.children && item2.children.length > 0">
                    <div class="menu-item-sub">@{{ item2.name }}</div>
                    <template v-for="(item3,index) in item2.children" :key="item3.id">
                        <div class="sub-menu" v-if="item3.children && item3.children.length > 0">
                            <div class="menu-item">@{{ item3.name }}</div>
                        </div>
                        <div class="menu-item" :class="{'curr':forActive === item3.key}" v-else  @click="handleMenuItem(item3,4)">@{{ item3.name }}</div>
                    </template>
                </div>
                <div class="menu-item" :class="{'curr':threeActive === item2.key}" v-else  @click="handleMenuItem(item2,3)">@{{ item2.name }}</div>
            </template>
        </div>
        <div class="menu-item" :class="{'curr':twoActive === item.key}" v-else  @click="handleMenuItem(item,2)">@{{ item.name }}</div>
        </template>

        <div class="user-nav-back" @click="menuShow =! menuShow"><i class="ri-arrow-left-double-line"></i></div>
    </div>
</div>
</template>
<script>
    const WwiUserMenu = {
        name: 'WwiUserMenu',
        template: '#WwiUserMenu',
        data(){
            return{
                menuList: @json($userMenus ?? []),
                activeMenu:'{{$activeKey ?? ''}}',
                oneActive: 'index',
                twoActive: '',
                threeActive:'',
                forActive: '',
                nowActive: '',
                clickItem: null,
                menuShow:false,
                expanded:[]
            }
        },
        mounted(){
            const openKeys = useUtilsConvertTreeData(useUtilsTopTreeNodes(JSON.parse(JSON.stringify(this.menuList)), 'key', this.activeMenu));
            if(openKeys && openKeys.length > 0){
                if (openKeys.length > 1) {
                    this.twoActive = openKeys[1].key;
                    this.nowActive = openKeys[1].key;
                }
                if (openKeys.length > 2) {
                    this.threeActive = openKeys[2].key;
                    this.nowActive = openKeys[2].key;
                }
                if (openKeys.length > 3) {
                    this.forActive = openKeys[3].key;
                }
                this.oneActive = openKeys[0].key;
            }
        },
        computed:{
            menuLevelList(){
                let menus = [];
                if (this.menuList.length > 0) {
                    this.menuList.forEach(item => {
                        if (this.oneActive === item.key && (item.children && item.children.length > 0)) {
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
                                if (this.threeActive === item2.key && (item2.children && item2.children.length > 0)) {
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
                if(this.menuAKey === item.key){
                    return;
                }
                this.oneActive = item.key;
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
                                this.threeActive = item.key;
                            }
                            if(menus[0].is_frame == 1) return window.open(shopwwiUserUrl  + menus[0].path);
                            return this.handleVueRouter(menus[0],step + 1);
                        }
                    }
                    return;
                }
                if(item.is_frame == 1) return window.open(shopwwiUserUrl  + item.path);

                return this.handleVueRouter(item,step);

            },
            handleVueRouter(item,step){
                if(step === 2) this.twoActive = item.key;
                if(step === 3) this.threeActive = item.key;
                if(step === 4) this.forActive = item.key;
                if(this.$router && item.is_cache ==1){
                    return this.$router.push(shopwwiUserPrefix + item.path);
                }
                return window.location.href = shopwwiUserUrl  + item.path;
            },
        }
    }
</script>