@extends('admin.layouts.app')
@section('title', $seoTitle ?? '')
@section('css')

@endsection
@section('content')

        <div class="px-4">
            <div class="cxd-Page-header border-b-0"><h2 class="cxd-Page-title"><span class="cxd-TplField"><span>素材中心</span></span></h2></div>
            <t-card :bordered="false">
                <div class="flex flex-col h-full overflow-hidden">
                    <div class="flex flex-row flex-1">
                        <div class="overscroll-contain h-full pr-4" style="width: 300px">
                            <div class="flex flex-row justify-between pb-4 items-center">
                                <span>相册分类</span>
                                <t-button theme="default" variant="base" @click="()=>{addAlbum = { pid:0, visible:true, title:'新增相册', name:'', id:0}; }">添加相册</t-button>
                            </div>

                            <div class="operations">
                                <t-input v-model="filterText" @change="onInputChange" placeholder="输入关键词搜索">
                                    <template #suffix>
                                        <i class="ri-search-line"></i>
                                    </template>
                                </t-input>
                            </div>
                            <t-tree :data="albumList" :keys="{value:'id', label:'name'}" :filter="filterByText" class="mt-4" activable v-model:actived="selectAlbum" @click="onAlbumClick">
                                <template #operations="{ node }">
                                    <t-space size="small">
                                        <t-button size="small" theme="default" @click="()=>{ addAlbum = {  visible:true, title:'编辑相册', name:node.data.name, id:node.data.id};}"><i class="ri-edit-line"></i></t-button>
                                        <t-button size="small" theme="danger" @click="()=>{ addAlbum.id = node.data.id;onAddDel(node)}"><i class="ri-close-line"></i></t-button>
                                    </t-space>

                                </template>
                            </t-tree>
                        </div>
                        <div class="flex-1 pl-4 border-l border-t-0 border-b-0 border-r-0 border-solid border-light overflow-auto">
                            <div style="min-width: 600px">
                                <div class="flex flex-row w-full justify-between pb-4">
                                    <t-space>
                                        <t-button @click="this.$refs.wwiUploadRef.open()">上传附件</t-button>
                                        <t-button @click="()=>{ moveAlbum.status =!moveAlbum.status;moveAlbum.type=1}" :disabled="selectList.length < 1">批量移动</t-button>
                                        <t-button theme="danger" :disabled="selectList.length < 1" @click="()=>{ moveAlbum.type=1; onMoveDel()}">批量删除</t-button>
                                    </t-space>
                                    <div class="">
                                        <t-input-group>
                                            <t-input v-model:value="search.original_name_like" placeholder="输入图片名称"></t-input>
                                            <t-button @click="()=>{ pageData.page = 1; getList() }">搜索</t-button>
                                        </t-input-group>
                                    </div>
                                </div>
                                <div style="min-height: 600px; max-height: 100%;" class="overflow-y-auto overflow-x-hidden">
                                    <t-row :gutter="[20,20]">
                                        <t-col v-for="(item,index) in filesList" :key="index" :span="12" :xs="6" :sm="3" :lg="2">
                                            <div class="w-full overflow-hidden">
                                                <div class="flex flex-row pb-4">
                                                    <t-popup>
                                                        <div class="w-0 flex-1 mr-2 line-clamp-1">@{{ item.original_name }}</div>
                                                        <template #content>
                                                            <div>@{{ item.original_name }}</div>
                                                        </template>
                                                    </t-popup>
                                                    <t-tag class="flex-shrink-0" :theme="isHas(item) ? 'primary' : 'default'" @click="handleSelect(item)">@{{ isHas(item) ? '已选择' : '选择' }}</t-tag>
                                                </div>
                                                <div class="relative w-full h-0 pt-100% box-border">
                                                    <video :src="item.fileUrl" class="align-middle absolute top-0 w-full h-full" controls v-if="item.files_type.indexOf('video') !== -1"></video>
                                                    <t-image-viewer :images="[item.fileUrl]" v-else>
                                                        <template #trigger="{ open }">
                                                            <t-image :src="item.fileUrl" class="align-middle absolute top-0 w-full h-full" :style="{ width: '100%', height: '100%' }" fit="contain" @click="open"/>
                                                        </template>
                                                    </t-image-viewer>
                                                </div>
                                                <div class="flex flex-row justify-between items-center pt-4">
                                                    <t-popup>
                                                        <span class="text-sm line-clamp-1">@{{ item.created_at }}</span>
                                                        <template #content>
                                                            <span>@{{ item.created_at }}</span>
                                                        </template>
                                                    </t-popup>
                                                    <t-dropdown trigger="click"
                                                                :options="[
                                                          {
                                                            content: '移动',
                                                            value: 'manage',
                                                            onClick: () => { moveAlbum.id = item.id; moveAlbum.status =!moveAlbum.status;moveAlbum.type=0 },
                                                          },
                                                          {
                                                            content: '删除',
                                                            value: 'delete',
                                                            onClick: () => { moveAlbum.id = item.id;moveAlbum.type=0;onMoveDel() },
                                                          },
                                                        ]">
                                                        <t-button theme="default"  shape="square" variant="text">
                                                            <i class="ri-more-2-fill"></i>
                                                        </t-button>
                                                    </t-dropdown>
                                                </div>
                                            </div>
                                        </t-col>
                                    </t-row>
                                </div>
                                <div class="flex justify-center pt-2">
                                    <t-pagination :page-size-options="[12,24,48]" v-model="pageData.page" v-model:page-size="pageData.limit" :total="dataNum" @change="(e)=>{ pageData.page = e.current; pageData.limit = e.pageSize; getList();}" />
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </t-card>
        </div>
        {{--转移素材--}}
        <t-dialog
                v-model:visible="moveAlbum.status"
                header="转移分类"
                attach="body"
                :confirm-on-enter="true"
                :on-confirm="onConfirmMove"
        >
            <t-tree :data="albumList" :keys="{value:'id', label:'name'}" activable v-model:actived="moveAlbum.album_id" />
        </t-dialog>
        <t-dialog
                v-model:visible="addAlbum.visible"
                :header="addAlbum.title"
                attach="body"
                :confirm-on-enter="true"
                :confirm-btn="{
                    content: addAlbumLoading?'保存中...':'提 交',
                    theme: 'primary',
                    loading: addAlbumLoading,
                  }"
                :on-confirm="onConfirmAdd"
        >
            <t-form :data="addAlbum">
                <t-form-item label="相册名称" name="name" :rules="[{ required: true}]">
                    <t-input v-model="addAlbum.name" placeholder="请输入内容"></t-input>
                </t-form-item>
            </t-form>

        </t-dialog>
        <wwi-upload is-video :album-id="search.album_id" @ok="()=>{ pageData.page = 1; getList() }" ref="wwiUploadRef"></wwi-upload>
@endsection
@section('js')
    <script>
        vueComponents = { WwiUpload };
        vueData = {
            search: {},
            pageData: {page: 1, limit: 12},
            selectList: [],
            showEdit: false,
            filesList: [],
            albumList:[],
            dataNum: 0,
            filterText:'',
            filterByText:null,
            moveAlbum:{ status:false, id:0, type:0, album_id:[]},
            addAlbum:{ pid:0, visible:false, title:'新增相册', name:''},
            addAlbumLoading :false,
            selectAlbum:[]
        };
        vueAppend = {
            mounted() {
                this.getAlbum();
                this.getList();
            }
        };

        vueMethods = {
            getList() {
                useFetch(this,"{{ shopwwiAdminUrl('system/files') }}",{params:{_format:'json', ...this.search, ...this.pageData}}).then(json=>{
                    this.filesList = json.data.items;
                    this.dataNum = json.data.total;
                })
            },
            getAlbum(){
                useFetch(this,"{{ shopwwiAdminUrl('system/album') }}",{params:{limit:1000,_format:'json'}}).then(json=>{
                    this.albumList = json.data.items;
                }).catch((err) => {
                    console.log(err)
                })
            },
            handleSelect(e) {
                let has = false;
                this.selectList.forEach((item, index) => {
                    if (item.id === e.id) {
                        this.selectList.splice(index, 1);
                        has = true;
                    }
                });
                if (has) {
                    return;
                }
                this.selectList.push(e);
            },
            isHas(data) {
                let has = false;
                this.selectList.forEach(item => {
                    if (item.id === data.id) {
                        has = true
                    }
                })
                return has
            },
            onInputChange(state){
                this.filterByText = (node) => {
                    const label = node?.data?.name || '';
                    const rs = label.indexOf(this.filterText) >= 0;
                    return rs;
                };
            },
            onConfirmMove(){
                let ids = this.moveAlbum.id;
                if(this.moveAlbum.type === 1){
                    ids = this.selectList.map(item=> {
                        return item.id;
                    })
                }
                const albumId = this.moveAlbum.album_id[0] ?? 0;
                if(albumId < 1){
                    this.$message.error('请选择转移相册');
                    return;
                }
                useFetch(this,"{{ shopwwiAdminUrl('system/files/move') }}/" + ids,{method:'POST',data:{ album_id:albumId }}).then((json) => {
                    this.$message.success('移动成功');
                    if(this.moveAlbum.type === 1) this.selectList = [];
                    this.moveAlbum.id = 0;
                    this.moveAlbum.status = false;
                    this.getList()
                })
            },
            onMoveDel(){
                let ids = this.moveAlbum.id;
                if(this.moveAlbum.type === 1){
                    ids = this.selectList.map(item=> {
                        return item.id;
                    })
                }
                const myDialog = this.$dialog({
                    header: '操作提示',
                    body: '确定要删除所选附件吗?删除后将不可恢复',
                    onConfirm:()=>{
                        myDialog.hide();
                        useFetch(this,"{{ shopwwiAdminUrl('system/files') }}/" + ids,{ method:'DELETE' }).then((json) => {
                            this.$message.success('删除成功');
                            if(this.moveAlbum.type === 1) this.selectList = [];
                            this.moveAlbum.id = 0;
                            myDialog.hide();
                        })
                    }
                })

            },
            onConfirmAdd(){
                this.addAlbumLoading = true;
                $url = "{{ shopwwiAdminUrl('system/album') }}";
                $opt = { method:'POST', data:{ name:this.addAlbum.name, pid: this.addAlbum.pid }};
                if(this.addAlbum.id > 0){
                    $url = "{{ shopwwiAdminUrl('system/album') }}/" + this.addAlbum.id;
                    $opt = { method:'PUT', data:{ name:this.addAlbum.name }};
                }
                useFetch(this,$url,$opt).then((json) => {
                    this.$message.success(this.addAlbum.id > 0?'修改成功':'新增成功');
                    this.getAlbum();
                    this.addAlbum.visible = false;
                }).finally(()=>this.addAlbumLoading = false)
            },
            onAddDel(node){
                let id = this.addAlbum.id;
                if(id < 1) {
                    this.$message.error('请选择相册删除');
                    return;
                }
                const myDialog = this.$dialog({
                    header: '操作提示',
                    body: '确定要删除相册吗?',
                    onConfirm:()=>{
                        useFetch(this,"{{ shopwwiAdminUrl('system/album') }}/" + id,{ method:'DELETE' }).then((json) => {
                            this.$message.success('删除成功');
                            this.addAlbum.id = 0;
                            this.getAlbum();
                            myDialog.hide();
                        })
                    }
                })
            },
            onAlbumClick({node,dom}){
                const albumId = this.selectAlbum[0] ?? 0;
                if(albumId){
                    if(node.value !== this.search.album_id){
                        this.search.album_id = node.value;
                        this.pageData.page = 1;
                        this.getList()
                    }
                }else{
                    if(this.search.album_id > 0){
                        this.search.album_id = '';
                        this.pageData.page = 1;
                        this.getList();
                    }
                }
            }
        };
    </script>
@endsection