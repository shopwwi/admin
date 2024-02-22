<?php

namespace Shopwwi\Admin\App\User\Controllers;

use Shopwwi\Admin\App\Admin\Models\SysMsgTplCommon;
use Shopwwi\Admin\App\Admin\Service\DictTypeService;
use Shopwwi\Admin\App\User\Models\UserMessageSetting;
use Shopwwi\Admin\App\User\Models\UserMsg;
use Shopwwi\Admin\App\User\Service\UserMsgService;
use Shopwwi\Admin\Libraries\Validator;
use Shopwwi\WebmanAuth\Facade\Auth;
use support\Db;
use support\Request;

class MessageController extends Controllers
{
    protected $model = UserMsg::class;
    protected $activeKey = 'account';

    /**
     * 获取列表
     * @param Request $request
     * @return \support\Response
     */
    public function index(Request $request)
    {
        $user = $this->user(true);
        if($this->format() == 'json'|| $this->format() == 'data'){

            $list = $this->getList(new $this->model,function ($q) use ($user) {
                return $q->where('user_del',0)->where('user_id',$user->id);
            },['is_read'=>'asc','id'=>'desc'],['user_id','user_del']);
            $list->map(function ($item){
                $item->tplClassName = DictTypeService::getRowDictByKey('userMessageClass',$item->tpl_class);
            });
            return shopwwiSuccess(['items'=>$list->items(),'total'=>$list->total(),'page'=>$list->currentPage(),'hasMore' =>$list->hasMorePages()]);
        }
        $options = DictTypeService::getAmisDictType('userMessageClass');
        $url = shopwwiUserUrl('/',true,true);
        $fullUrl = shopwwiUserUrl('/');
        $page =$this->basePage()->body([
            shopwwiAmis('html')->html(<<<HTML
                <style>
                .amis-scope .cxd-List-fixedTop{ background: transparent}
                </style>
            HTML),
            shopwwiAmis('tabs')->tabs([['title'=>'消息列表','body'=>[
                shopwwiAmis('button-group-select')->name('status')->value(request()->input('tpl_class',''))->options([
                    ['label'=>'所有消息','value'=> ''],...$options
                ])->onEvent(['change'=>[
                    "actions" => [
                        ['componentId' => 'crud','actionType' => 'reload','data'=>['tpl_class'=>'$value']]
                    ]
                ]]),
                UserMsgService::getIndexAmis()
            ]],['title'=>'消息设置']])->onEvent([
                'change' => [
                    'actions' => [
                        [
                            'actionType' => 'custom',
                            'script' => <<<JS
                                 const url = event.data.value == 1 ? 'message': 'message/setting';
                                if(this.wwiRouter){
                                    this.wwiRouter.push('{$url}' + url)
                                }else{
                                    doAction({actionType: 'link', args: {link: '{$fullUrl}/' + url}});
                                }
                        JS
                        ]
                    ]
                ]
            ])
            ]);
        if($this->format() == 'web') return shopwwiSuccess($page);
        return $this->getUserView(['seoTitle'=>'我的消息','menuActive'=>'','json'=>$page]);
    }

    /**
     * 获取详情
     * @param Request $request
     * @param $id
     * @return \support\Response
     */
    public function show(Request $request,$id)
    {
        try {
            $url = '/';
            $info = UserMsg::where('id',$id)->first();
            if($info == null) throw new \Exception('不存在');
            if($info->is_read == 0){
                $info->is_read = 1;
                $info->save();
            }
            if($this->format() == 'json') return shopwwiSuccess($info);
            // 获得消息对应链接

            return shopwwiSuccess(['url' => $url]);
        }catch (\Exception $e){
            return shopwwiError($e->getMessage());
        }

    }

    /**
     * 批量设置为已读
     * @param Request $request
     * @param $id
     * @return \support\Response
     * @throws \Throwable
     */
    public function read(Request $request,$id)
    {
        $ids = is_array($id) ? $id : (is_string($id) ? explode(',', $id) : func_get_args());
        $user = Auth::guard($this->guard)->fail()->user(true);
        try {
            Db::connection()->beginTransaction();
            (new $this->model)->whereIn($this->key,$ids)->where('user_id',$user->id)->update(['is_read'=>1]);

            Db::connection()->commit();
            return shopwwiSuccess([],'成功');
        } catch (\Exception $e) {
            Db::connection()->rollBack();
            return shopwwiError($e->getMessage());
        }
    }

    /**
     * 删除消息
     * @param Request $request
     * @param $id
     * @return \support\Response
     * @throws \Throwable
     */
    public function destroy(Request $request,$id)
    {
        $ids = is_array($id) ? $id : (is_string($id) ? explode(',', $id) : func_get_args());
        $user = Auth::guard($this->guard)->fail()->user(true);
        try {
            Db::connection()->beginTransaction();
            (new $this->model)->whereIn($this->key,$ids)->where('user_id',$user->id)->update(['user_del'=>1]);

            Db::connection()->commit();
            return shopwwiSuccess([],trans('del',[],'messages').trans('success',[],'messages'));
        } catch (\Exception $e) {
            Db::connection()->rollBack();
            return shopwwiError($e->getMessage());
        }
    }

    /**
     * 未读消息统计
     * @return \support\Response
     */
    public function count(){
        try {
            $user = $this->user(true);
            $count = UserMsg::where('user_id',$user->id)->where('is_read',0)->count();
            return shopwwiSuccess(['count'=>$count]);
        }catch (\Exception $e){
            return shopwwiError($e->getMessage());
        }
    }

    public function setting(Request $request){
        if($request->method() == 'POST'){
            return $this->saveSetting($request);
        }
        try {

            $user = $this->user(true);
            $msgList = SysMsgTplCommon::where('type',1)->select('name','code','class')->orderBy('class','asc')->get();
            $setList = UserMessageSetting::whereIn('tpl_code',$msgList->pluck('code'))->where('user_id',$user->id)->get();
            $msgList->map(function ($item) use ($setList) {
                $has = $setList->where('tpl_code',$item->code)->first();
                $item->is_receive = 0;
                if($has == null || $has->status == 1){
                    $item->is_receive = 1;
                }
                $item->className = DictTypeService::getRowDictByKey( 'userMessageClass',$item->class);
            });
            if($this->format() == 'json'){
                return shopwwiSuccess(['items'=> $msgList]);
            }
            $yesOrNo = DictTypeService::getAmisDictType('yesOrNo');
            $url = shopwwiUserUrl('/',true,true);
            $fullUrl = shopwwiUserUrl('/');
            $page = $this->basePage()->body([
                shopwwiAmis('tabs')->defaultKey(1)->tabs([['title'=>'消息列表'],['title'=>'消息设置','body'=>shopwwiAmis('service')->api(shopwwiUserUrl('message/setting?_format=json'))->body([
                    shopwwiAmis('table')->id('crud')->affixHeader(false)
                        ->columns([
                            shopwwiAmis()->name('name')->label('模板名称'),
                            shopwwiAmis()->name('className')->label('消息类型'),
                            shopwwiAmis('tpl')->label('接收方式')->tpl('站内信'),
                            shopwwiAmis('mapping')->name('is_receive')->label('是否接收')->map(DictTypeService::toMappingSelect($yesOrNo,'${is_receive}')),
                            shopwwiAmis('operation')->label('操作')->align('center')->style(['verticalAlign'=>'top'])->width(120)->buttons([
                                shopwwiAmis('button')->label('设置')->actionType('dialog')->dialog([
                                    'title'=>'消息接收设置',
                                    'body'=>[
                                        shopwwiAmis('form')->api('post:' . shopwwiUserUrl('message/setting'))->body([
                                            shopwwiAmis('hidden')->name('code'),
                                            shopwwiAmis('radios')->name('is_receive')->label('接收设置')->options([['label'=>'接收','value'=>1],['label'=>'不接收','value'=>0]]),
                                        ])]
                                ])->level('light')->icon("ri-edit-fill")
                            ])
                        ]),
                ])]])
                    ->onEvent([
                        'change' => [
                            'actions' => [
                                [
                                    'actionType' => 'custom',
                                    'script' => <<<JS
                                     const url = event.data.value == 1 ? 'message': 'message/setting';
                                    if(this.wwiRouter){
                                        this.wwiRouter.push('{$url}' + url)
                                    }else{
                                        doAction({actionType: 'link', args: {link: '{$fullUrl}/' + url}});
                                    }
                                JS
                                ]
                            ]
                        ]
                    ]),
            ]);
            if($this->format() == 'web'){
                return shopwwiSuccess($page);
            }
            return $this->getUserView(['json'=>$page,'menuActive'=>'','seoTitle'=>'消息设置']);
        }catch (\Exception $e){
            return $this->backError($e);
        }
    }
    private function saveSetting($request){
        $validator = Validator::make($request->all(), [
            'is_receive' => 'bail|required|numeric|in:0,1',
            'code' => 'bail|required'
        ], [], [
            'is_receive' => '接收设置',
            'code' => '消息类型'
        ]);
        if ($validator->fails()) {
            return shopwwiValidator('数据验证失败', $validator->errors());
        }
        try {
            $user = $this->user(true);
            $params = shopwwiParams(['is_receive'=>0,'code']);
            // 设置消息
            UserMessageSetting::updateOrCreate([
                'tpl_code' => $params['code'],'user_id'=>$user->id
            ],['status'=>$params['is_receive']]);

            return shopwwiSuccess();
        }catch (\Exception $e){
            return shopwwiError($e->getMessage());
        }
    }
}