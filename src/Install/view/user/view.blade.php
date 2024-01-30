@extends('user.layouts.app')
@section('title', $seoTitle ?? '')
@section('css')
    <script src="/static/sdk/sdk.js"></script>
@endsection
@section('content')
    <wwi-amis :schema="schema"></wwi-amis>
@endsection
@section('js')
    <script>
        vueComponents = { WwiAmis };
        vueData = { schema: @json($json??[]) };
        vueMethods = {
            getNowInfo(){
                useFetch(this,window.location.href,{params:{_format:'web'}}).then(res=>{
                    this.schema = res.data;
                    if(res.data.title){
                        document.title = res.data.title;
                    }
                }).catch(err=>{
                    if(err.status && err.status === 401){
                        window.location.href = '{{ shopwwiUserUrl('auth/login') }}';
                    }
                })
            }
        };
        vueAppend = {
            created(){
                window.wwiRouter = this.$router;
            },
            watch: {
                '$route.path'(newVal,oldVal){
                    if(oldVal !== '/'){
                        this.getNowInfo()
                    }
                }
            },
        };
    </script>
@endsection
@section('js2')
    <script src="/static/unpkg/vue/vue-router.js"></script>
    <script>
        wwiApp.use(VueRouter.createRouter({
            history: VueRouter.createWebHistory(),routes:[{ path: '/:path(.*)*',component:{template:`<router-view></router-view>`} }]
        }))
    </script>
@endsection