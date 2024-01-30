(function () {
    const theme = domGetCookie('shopwwiAdminTheme');
    if(theme === 'dark'){
        document.documentElement.setAttribute('theme-mode', 'dark');
    }else{
        document.documentElement.removeAttribute('theme-mode');
    }
})();
function useUtilsTopTreeNodes(list,keyCol,keyVal){
    let newList = [];
    list.forEach(item=>{
        if(item.children !== undefined && item.children.length !== 0){
            let leaf = useUtilsTopTreeNodes(item.children,keyCol,keyVal);
            if(leaf !== undefined){
                item.children = leaf;
                newList.push(item);
            }else{
                if(item[keyCol] === keyVal){
                //    delete item['children'];
                    newList.push(item);
                }
            }
        }else{
            if(item[keyCol] === keyVal){
                newList.push(item);
            }
        }
    });
    if(newList != undefined && newList.length != 0){
        return newList;
    }
}

function useUtilsTabs(dom,className,cheep = false,object=(key,open)=>{}){
    const nodeList = document.querySelectorAll(dom); // 获取菜单元素
    for(let i = 0; i <  nodeList.length; i++){
        nodeList[i].onclick = function (){
            $key = this.getAttribute('data-key');
            if(cheep){
                this.classList.add(className);
                for (let j = 0; j < nodeList.length; j++){
                    if(nodeList[j] !== this){
                        nodeList[j].classList.remove(className);
                    }
                }
                object($key,true)
            }else{
                if(this.classList.contains(className)){
                    this.classList.remove(className);
                    object($key,false)
                }else{
                    this.classList.add(className);
                    object($key,true)
                }
            }

        }
    }
}

// 默认数据组件
var WwiChooseFiles = (function (vue) {
    'use strict';
    return  vue.defineComponent({
        name: 'WwiChooseFiles',
        template: `
    <div class="flex flex-row flex-1 h-full">
        <div class="w-40 overscroll-contain h-full border-r border-t-0 border-b-0 border-l-0 border-solid border-light">
            <t-tree :data="albumList" :keys="{value:'id', label:'name'}" class="mt-4" activable v-model:actived="selectAlbum" @click="onAlbumClick"></t-tree>
        </div>
        <div class="flex-1 pl-4 overflow-x-auto overflow-y-hidden mt-4 flex flex-col">
            <div class="flex-1 wwi-files-box">
                <div class=" grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    <div class="w-full cursor-pointer overflow-hidden" @click="handleSelect(item)" v-for="(item,index) in imageList" :key="index">
                        <div class="border-2 relative w-full h-0 pt-100% box-border border-solid mb-2" :class="isHas(item) ?'border-primary':'border-transparent hover:admin-border-primary-300'">
                            <div class="absolute top-0 w-full h-full z-10 text-primary" v-if="isHas(item)">
                                <i class="ri-checkbox-circle-fill  text-2xl mt-1 ml-1"></i>
                            </div>
                            <video :src="item.fileUrl" class="align-middle absolute top-0 w-full h-full" controls  v-if="item.files_type.indexOf('video') !== -1" ></video>
                            <t-image :src="item.fileUrl" class="align-middle absolute top-0 w-full h-full" :style="{ width: '100%', height: '100%' }" fit="cover" v-else />
                        </div>
                        <t-popup>
                            <div class="line-clamp-1">{{ item.original_name }}</div>
                            <template #content>
                                <div>{{ item.original_name }}</div>
                            </template>
                        </t-popup>
                    </div>
                </div>

            </div>
            <t-pagination class="pt-2" size="small" :page-size-options="[12,24,48]" v-model="pageData.page" v-model:page-size="pageData.limit" :total="dataNum" @change="(e)=>{ pageData.page = e.current; pageData.limit = e.pageSize; getList();}" />
        </div>
    </div>
        `,
        data(){
            return {
                search:{},
                pageData:{ page:1, limit:12 },
                selectList: this.defaultValue ?? [],
                showEdit:false,
                imageList:[],
                dataNum:0,
                selectAlbum:[],
                albumList:[]
            }
        },
        props:{
            num:{
                type: Number,
                default: 100
            },
            select:{
                type:Boolean,
                default: true
            },
            filesType:{
                type:[ Number, String],
                default: 'image'
            },
            defaultValue:{
                type:[Array,Object],
                default:[]
            }
        },
        mounted(){
            this.getList();
            this.getAlbum()
        },
        methods:{
            handleClick(){
                this.$message.success('重置成功');
            },
            getAlbum(){
                useFetch(this,shopwwiAdminUrl + "/system/album",{params:{limit:1000,_format:'json'}}).then(json=>{
                    this.albumList = json.data.items;
                }).catch((err) => {
                    console.log(err)
                })
            },
            getList(opt){
                this.search.files_type_like = this.filesType;
                useFetch(this,shopwwiAdminUrl + "/system/files",{params:{_format:'json', ...this.search, ...this.pageData}}).then(json=>{
                    this.imageList = json.data.items;
                    this.dataNum = json.data.total;
                })
            },
            handleSelect(e){
                if(!this.select){return;}
                let has = false;
                this.selectList.forEach((item,index)=>{
                    if(item.id === e.id){
                        this.selectList.splice(index,1);
                        has = true;
                    }
                });
                if(has){
                    return;
                }
                if(this.selectList.length >= this.num){
                    return;
                }
                this.selectList.push(e);
            },
            isHas(data){
                let has = false;
                this.selectList.forEach(item=>{
                    if(item.id === data.id){
                        has = true
                    }
                })
                return has
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
        }
    });
}(Vue));

// 默认数据组件
var WwiChoose = (function (vue) {
    'use strict';
    return  vue.defineComponent({
        name: 'WwiChoose',
        template: `
    <t-dialog v-model:visible="showModal" v-if="showModal" header="选择附件" attach="body" class="wwi-files-modal" :on-close="cancelCallback" :on-confirm="submitCallback" >
        <t-tabs v-model="tabIndex">
            <t-tab-panel v-for="tab in tabList" :key="tab.value" :value="tab.value" :label="tab.label">
                <div style="height: 500px">
                    <t-upload class="pt-4"
                              :action="getAction"
                              :placeholder="getPlaceholder" :accept="getAccept"
                              theme="file-flow"
                              multiple allowUploadDuplicateFile v-if="tabIndex === 'upload'"
                    ></t-upload>
                    <template v-if="!showSelect && tabIndex !== 'upload'">
                        <wwi-choose-files :filesType="tabIndex" :defaultValue="selectList[tabIndex]" :num="indexNum" v-model:value="selectList[tabIndex]" />
                    </template>
                    <div class="wwi-files-box p-4" style="height: 500px" v-else>
                        <vuedraggable
                                v-model="selectList[tabIndex]"
                                animation="300"
                                item-key="id"
                                filter=".no-draggable"
                                class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4"
                        >
                            <template #item="{ element,index }">
                                <div class="w-full cursor-pointer" @click="handleSelectCancel(element,index)">
                                    <div class="border-2 relative w-full h-0 pt-100% box-border border-solid mb-2 border-primary">
                                        <div class="absolute top-0 w-full h-full z-10 text-primary">
                                            <i class="ri-checkbox-circle-fill  text-2xl mt-1 ml-1"></i>
                                        </div>
                                        <video :src="element.fileUrl" class="align-middle absolute top-0 w-full h-full" controls v-if="element.files_type.indexOf('video') !== -1" ></video>
                                        <t-image :src="element.fileUrl" class="align-middle absolute top-0 w-full h-full" fit="cover" v-else ></t-image>
                                    </div>
                                    <div class="line-clamp-1">{{ element.original_name }}</div>
                                </div>
                            </template>
                        </vuedraggable>
                    </div>
                </div>
            </t-tab-panel>
        </t-tabs>
        <template #footer>
            <div class="flex flex-row flex-1 justify-between">
                <div class="">
                    <div v-if="tabIndex !=='upload'">
                        已选择：<span class="text-primary font-bold cursor-pointer" @click="showSelect = true"> {{ selectList[tabIndex].length ?? 0 }} / {{ indexNum }} 项</span>
                        <span class="text-primary font-bold cursor-pointer ml-4"  @click="showSelect = false" v-if="showSelect">返回全部</span></div>
                </div>
                <t-space>
                    <t-button theme="default" @click="cancelCallback">取消</t-button>
                    <t-button @click="submitCallback">确认选择</t-button>
                </t-space>
            </div>
        </template>
    </t-dialog>
        `,
        components:{WwiChooseFiles,vuedraggable},
        data(){
            return {
                showModal: false,
                showSelect: false,
                tabIndex: this.type ?? 'image',
                selectList: { image:[], video:[], icon:[]},
                indexNum: this.num,
                showFiles:{image: this.showImage, video: this.showVideo, icon: this.showIcon}
            }
        },
        props:{
            type:{
                type:String,
                default:'image'
            },
            showImage:{
                type:Boolean,
                default:true
            },
            showVideo:{
                type:Boolean,
                default:false
            },
            showIcon:{
                type:Boolean,
                default:false
            },
            num:{
                type:Number,
                default:100
            }
        },
        mounted(){
            // this.indexNum = 10
        },
        emits:['ok'],
        computed:{
            getPlaceholder(){
                let tips = "请选择上传图片,可批量上传，单张图片不得大于10M";
                if(this.showFiles.video) tips = "请选择上传视频,可批量上传，单个视频不得大于30M";
                if(this.showFiles.video && this.showFiles.image) tips = "支持批量上传图片及视频，单张图片不得大于10M，视频不得大于30M";
                return tips;
            },
            getAccept(){
                let accept = 'image/*';
                if(this.showFiles.video) accept = "video/*";
                if(this.showFiles.video && this.showFiles.image) accept = "image/*|video/*";
                return accept;
            },
            getAction(){
                const url = shopwwiAdminUrl +"/system/files/upload";
                let type = 0;
                if(this.showFiles.video) type = 1;
                if(this.showFiles.video && this.showFiles.image) type = 2;
                return url + '?' + new URLSearchParams({type:type,albumId:0});
            },
            tabList(){
                const list = [{ value: 'upload',label:'上传附件'}];
                if(this.showFiles.image){
                    list.push({ value: 'image',label:'选择图片'})
                }
                if(this.showFiles.video){
                    list.push({ value: 'video',label:'选择视频'})
                }
                if(this.showFiles.icon){
                    list.push({ value: 'icon',label:'选择图标'})
                }
                return list;
            }
        },
        methods:{
            open(e={}){
                if(e.num) this.indexNum = e.num;
                this.showModal = true;
            },
            cancelCallback(){
                this.showModal = false;
            },
            submitCallback(){
                if(this.selectList[this.tabIndex].length < 1){
                    this.$message.error('未选择数据');
                    return;
                }
                this.$emit('ok',this.selectList[this.tabIndex],this.tabIndex);
                this.showModal = false;
            },
            handleSelectCancel(item,index){
                this.selectList[this.tabIndex].splice(index,1);
            }
        }
    });
}(Vue));

// 默认数据组件
var WwiImage = (function (vue) {
    'use strict';
    return  vue.defineComponent({
        name: 'WwiImage',
        template: `
            <div class="t-textarea__inner items-center justify-center" :style="{width:width,height:height}">
                <div class="w-full h-full flex items-center justify-center" style="cursor: pointer;" v-if="!modelValue" @click="$refs.imageRef.open()">
                    <t-tooltip :content="tips">
                        <div variant="text"><t-icon name="plus"></t-icon>添加图片</div>
                    </t-tooltip>
                </div>
                <div class="w-full h-full relative viewer__hover" v-else>
                    <t-image-viewer :images="[url]">
                        <template #trigger="{ open }">
                            <t-image :src="url"  class="align-middle absolute top-0 w-full h-full" fit="cover">
                                <template #overlay-content>
                                    <div class="viewer--hover flex items-center">
                                        <t-space direction="vertical">
                                            <div @click="open"><t-icon name="browse" class="text-light"></t-icon>预览</div>
                                            <div @click="$refs.imageRef.open()" size="small">
                                                <t-icon name="swap" class="text-light"></t-icon>替换
                                            </div>
                                        </t-space>
                                        <div @click="handleDel" :style="{ position: 'absolute', right: '-5px', top: '-5px', borderRadius: '3px' }">
                                            <t-icon name="close-circle" class="text-danger"></t-icon>
                                        </div>
                                    </div>
                                </template>
                            </t-image>
                        </template>
                    </t-image-viewer>
                </div>
            </div>
            <wwi-choose :num="1"  ref="imageRef" @ok="handleImageList" ></wwi-choose>
        `,
        components:{WwiChoose},
        data(){
            return {

            }
        },
        props:{
            width:{ type:String, default:'100px'},
            height:{ type:String, default:'100px'},
            modelValue:{ type:String },
            url:{ type:String },
            tips:{ type:String, default:'建议尺寸100*100' }
        },
        emits:['update:modelValue','update:url'],
        methods:{
            handleImageList(e){
                this.$emit('update:modelValue',e[0].name);
                this.$emit('update:url',e[0].fileUrl);
            },
            handleDel(){
                this.$emit('update:modelValue', null);
                this.$emit('update:url', null);
            }
        }
    });
}(Vue));

// 默认数据组件
var WwiUpload = (function (vue) {
    'use strict';
    return  vue.defineComponent({
        name: 'WwiUpload',
        template: `
            <t-dialog
                  v-model:visible="visible"
                  header="上传附件"
                  attach="body"
                  :confirm-on-enter="true"
                    width="50%"
                    :on-close="onSubmit"
                  :on-confirm="onSubmit"
                >
                    <t-upload
                      :action="getAction"
                      :placeholder="getPlaceholder" :accept="getAccept"
                      theme="file-flow"
                      multiple allowUploadDuplicateFile
                    ></t-upload>
            </t-dialog>
        `,
        data(){
            return {
                visible:false
            }
        },
        props:{
            isImage:{type:Boolean,default:true},
            isVideo:{type:Boolean,default:false},
            albumId:{type:Number,default:0}
        },
        emits: ['ok'],
        computed:{
            getPlaceholder(){
                let tips = "请选择上传图片,可批量上传，单张图片不得大于10M";
                if(this.isVideo) tips = "请选择上传视频,可批量上传，单个视频不得大于30M";
                if(this.isVideo && this.isImage) tips = "支持批量上传图片及视频，单张图片不得大于10M，视频不得大于30M";
                return tips;
            },
            getAccept(){
                let accept = 'image/*';
                if(this.isVideo) accept = "video/*";
                if(this.isVideo && this.isImage) accept = "image/*|video/*";
                return accept;
            },
            getAction(){
                const url = shopwwiAdminUrl + "/system/files/upload";
                let type = 0;
                if(this.isVideo) type = 1;
                if(this.isVideo && this.isImage) type = 2;
                return url + '?' + new URLSearchParams({type:type,albumId:this.albumId});
            }
        },
        methods:{
            onSubmit(){
                this.visible = false;
                this.$emit('ok', true)
            },
            open(){
                this.visible = true;
            }
        }
    });
}(Vue));

var WwiRichText = (function (vue) {
    'use strict';
    return  vue.defineComponent({
        name:'WwiRichText',
        template: `
            <div class="flex flex-col">
            <Editor  v-model="editorValue" width="100%" :plugins="plugins" ref="wwiEditorRef"
                     :toolbar="toolbar"
                     :init="{
                                        language_url: '/static/unpkg/tinymce/langs/zh-Hans.js',menubar:false,
                                        language: 'zh-Hans',toolbar_mode : 'wrap',
                                        min_height:600, max_height:1000,
                                        skin_url: '/static/unpkg/tinymce/skins/ui/oxide', resize: 'both',
                                        branding: false,
                                     }" ></Editor>
            <t-space class="mt-4">
                <t-button @click="$refs.imageRef.open()">插入图片</t-button>
                <t-button @click="$refs.videoRef.open()">插入视频</t-button>
            </t-space>
            <wwi-choose :num="100" ref="imageRef" @ok="handleImageList" ></wwi-choose>
            <wwi-choose :num="100" show-video :show-image="false" type="video" ref="videoRef" @ok="handleVideoList" ></wwi-choose>
        </div>
        `,
        components: {Editor,WwiChoose},
        props:{
            apiKey: String,
            cloudChannel: String,
            id: String,
            init: Object,
            initialValue: String,
            inline: Boolean,
            modelEvents: [String, Array],
            plugins:{
                type:[String, Array],
                default:'code print preview searchreplace autolink directionality visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor insertdatetime advlist lists wordcount imagetools textpattern help autosave autoresize'
            } ,
            tagName: String,
            toolbar:{
                type:[String, Array],
                default:'code undo redo restoredraft | cut copy paste pastetext | forecolor backcolor bold italic underline strikethrough link anchor | alignleft aligncenter alignright alignjustify outdent indent | styleselect fontselect fontsizeselect | bullist numlist | blockquote subscript superscript removeformat | table image media charmap hr pagebreak insertdatetime print preview | lineheight fullscreen'
            },
            disabled: Boolean,
            tinymceScriptSrc: String,
            outputFormat: {
                type: String,
                validator: function (prop) { return prop === 'html' || prop === 'text'; }
            },
        },
        data(){
            return {
                editorValue:this.modelValue??''
            }
        },
        methods:{
            handleImageList(files){
                console.log()
                files.forEach(item=>{
                    this.$refs.wwiEditorRef.getEditor().insertContent(`<img src='${item.fileUrl}' alt='${item.file_name}' width='100%' />`)
                })
            },
            handleVideoList(files){
                files.forEach(item=>{
                    this.$refs.wwiEditorRef.getEditor().insertContent(`<video controls="controls"><source src="${item.fileUrl}"></video>`)
                })
            }
        }
    });
}(Vue));