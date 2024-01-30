<?php
/**
 *-------------------------------------------------------------------------s*
 * 会员经验值日志控制器
 *-------------------------------------------------------------------------h*
 * @copyright  Copyright (c) 2015-2022 Shopwwi Inc. (http://www.shopwwi.com)
 *-------------------------------------------------------------------------o*
 * @license    http://www.shopwwi.com        s h o p w w i . c o m
 *-------------------------------------------------------------------------p*
 * @link       http://www.shopwwi.com by 无锡豚豹科技
 *-------------------------------------------------------------------------w*
 * @since      ShopWWI智能管理系统
 *-------------------------------------------------------------------------w*
 * @author      8988354@qq.com TycoonSong
 *-------------------------------------------------------------------------i*
 */
namespace Shopwwi\Admin\App\Admin\Controllers\User;

use Shopwwi\Admin\Amis\Operation;
use Shopwwi\Admin\App\Admin\Service\AmisService;
use Shopwwi\Admin\App\Admin\Service\DictTypeService;
use Shopwwi\Admin\App\Admin\Service\SysConfigService;
use Shopwwi\Admin\Libraries\Amis\AdminController;
use support\Request;

class UserGrowthLogController extends AdminController
{
    public $model = \Shopwwi\Admin\App\User\Models\UserGrowthLog::class;
    public  $orderBy = ['id' => 'desc'];
    protected $trans = 'userGrowthLog'; // 语言文件名称
    protected $queryPath = 'user/growth'; // 完整路由地址
    protected $activeKey = 'userRightsGrowth';
    protected $useHasCreate = false;

    /**
     * 路由注册
     * @var string
     */
     public $routePath = 'growth'; // 当前路由模块不填写则直接控制器名
    // public $routeAction = ['index'=>['GET','POST']]; //方法注册 未填写的则直接any
     public $routeNoAction = ['create','store','update','edit','destroy','recovery']; //不允许方法注册
    // public $noNeedLogin = []; //不需要登入
    // public $noNeedAuth = []; //需要登入不需要鉴权
    /**
     * 数据字段处理
     * @return array
     */
    protected function fields(){
        $growthOperationStage = DictTypeService::getAmisDictType('growthOperationStage');
        return [
            shopwwiAmisFields(trans('field.id',[],'messages'),'id')->tableColumn(['width'=>60,'sortable'=>true])->showOnUpdate(0)->showOnCreation(0)->showFilter(),
            shopwwiAmisFields(trans('field.user_id',[],'userGrowthLog'),'user_id')->tableColumn(['sortable'=>true,'type'=>'tpl','tpl'=>'<span class=\'cxd-Tag\'>${user.nickname}(ID:${user_id})</span>'])
                ->filterColumn('picker',['joinValues'=>true,'source'=>shopwwiAdminUrl('users/list?_format=json'),'size'=>'lg','valueField'=>'id','labelField'=>'nickname','pickerSchema'=>AmisService::getUserList()]),
            shopwwiAmisFields(trans('field.operation_stage',[],'userGrowthLog'),'operation_stage')->showOnUpdate(0)->showOnCreation(0)->tableColumn(['sortable'=>true,'type'=>'mapping','map'=>$this->toMappingSelect($growthOperationStage,'${operation_stage}','default')])
                ->filterColumn('select',['options'=>$growthOperationStage])
                ->column('select',['options'=>$growthOperationStage]),
            shopwwiAmisFields(trans('field.growth',[],'userGrowthLog'),'growth')->tableColumn(['classNameExpr'=>"<%= data.growth > 0 ? 'text-danger' : 'text-success' %>"]),
            shopwwiAmisFields(trans('field.description',[],'userGrowthLog'),'description')->rules('required'),
            shopwwiAmisFields(trans('field.sys_user_id',[],'userGrowthLog'),'sys_user_id')
                ->showOnUpdate(0)->showOnCreation(0)->tableColumn(['sortable'=>true,'type'=>'tpl','tpl'=>'<span class=\'cxd-Tag\'>${sys_user_name}(ID:${sys_user_id})</span>'])->filterColumn('picker',['joinValues'=>true,'source'=>shopwwiAdminUrl('system/user/list?_format=json'),'size'=>'lg','valueField'=>'id','labelField'=>'nickname','pickerSchema'=>AmisService::getSysUserList()]),
            shopwwiAmisFields(trans('field.sys_user_name',[],'userGrowthLog'),'sys_user_name')->showOnUpdate(0)->showOnCreation(0)->showOnIndex(2),
            shopwwiAmisFields(trans('field.created_at',[],'messages'),'created_at')->tableColumn(['width'=>145,'sortable'=>true])->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'created_at_betweenAmisTime'])->showOnUpdate(0)->showOnCreation(0),
            shopwwiAmisFields(trans('field.updated_at',[],'messages'),'updated_at')->tableColumn(['width'=>145,'toggled'=>false,'sortable'=>true])->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'updated_at_betweenAmisTime'])->showOnUpdate(0)->showOnCreation(0),
        ];
    }
    /**
     * 添加关联关系
     * @param $model
     * @return mixed
     */
    protected function beforeJsonList($model){
        $model = $model->select($this->filterJsonFiled());
        $model->with(['user'=>function($q){
            $q->select('id','username','avatar','nickname');
        }]);
        return $model;
    }

    protected function operation()
    {
        return Operation::make()->label(trans('actions',[],'messages'))->fixed('right')->width(160)->buttons([
            $this->rowShowButton(1),
            $this->rowDeleteButton(),
        ]);
    }

    /**
     * 经验值规则设置
     * @param Request $request
     * @return \support\Response
     */
    public function setting(Request $request)
    {
        try {
            if($request->method() === 'GET'){
                if($this->format() == 'json'){
                    $info = SysConfigService::getFirstOrCreate([
                        'key' => 'growth'
                    ],['name'=>'成长值','value'=>[
                        'used' => '1',
                        'rules' => [
                            ['label'=>'register','value'=>5,'desc'=>'当会员注册成功后将获得相应的成长值'],
                            ['label'=>'login','value'=>5,'desc'=>'当会员每天第一次登录成功后将获得相应的成长值']
                        ],
                    ]]);
                    return shopwwiSuccess($info);
                }
                $openOrClose = DictTypeService::getAmisDictType('openOrClose');
                $pointOrGrowthRuleType = DictTypeService::getAmisDictType('pointOrGrowthRuleType');
                $form = $this->baseForm()->body([
                    shopwwiAmis('grid')->gap('lg')->columns([
                        shopwwiAmis('radios')->name('growth.used')->label(trans('config.used',[],$this->trans))->selectFirst(true)->options($openOrClose)->xs(12),
                        shopwwiAmis('combo')->name('growth.rules')->label(trans('config.rules',[],$this->trans))->multiple(true)->draggable(true)->items([
                            shopwwiAmis('select')->name('label')->options($pointOrGrowthRuleType),
                            shopwwiAmis('input-text')->name('value'),
                            shopwwiAmis('input-text')->name('desc'),
                        ]),
                    ])
                ])->api('post:' . shopwwiAdminUrl('user/growth/setting'))
                    ->actions([
                        shopwwiAmis('reset')->label(trans('reset',[],'messages')),
                        shopwwiAmis('submit')->label(trans('submit',[],'messages'))->level('primary')
                    ])
                    ->initApi(shopwwiAdminUrl('user/growth/setting?_format=json'));
                $page = $this->basePage()->body($form);
                $page = $page->subTitle(trans('config.title',[],$this->trans));
                if($this->format() == 'data' || $this->format() == 'web') return shopwwiSuccess($page);
                return $this->getAdminView(['json'=>$page,'activeKey'=>'userRightsSite']);
            }else{
                $params = shopwwiParams(['growth']);
                SysConfigService::updateSetting($params);
            }
            return shopwwiSuccess();
        }catch (\Exception $e){
            return shopwwiError($e->getMessage());
        }
    }
}
