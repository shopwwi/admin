<?php
/**
 *-------------------------------------------------------------------------s*
 * 会员积分日志控制器
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

class UserPointLogController extends AdminController
{
    public $model = \Shopwwi\Admin\App\User\Models\UserPointLog::class;
    public  $orderBy = ['id' => 'desc'];

    protected $trans = 'userPointLog'; // 语言文件名称
    protected $queryPath = 'user/point'; // 完整路由地址
    protected $activeKey = 'userPointIndex';
    protected $useHasCreate = false;

    /**
     * 路由注册
     * @var string
     */
     public $routePath = 'point'; // 当前路由模块不填写则直接控制器名
    // public $routeAction = ['index'=>['GET','POST']]; //方法注册 未填写的则直接any
     public $routeNoAction = ['create','store','update','edit','destroy','recovery','restore','erasure']; //不允许方法注册
    // public $noNeedLogin = []; //不需要登入
    // public $noNeedAuth = []; //需要登入不需要鉴权
    /**
     * 数据字段处理
     * @return array
     */
    protected function fields(){
        $balanceOperationStage = DictTypeService::getAmisDictType('pointOperationStage');
        return [
            shopwwiAmisFields(trans('field.id',[],'messages'),'id')->tableColumn(['width'=>60,'sortable'=>true])->showOnUpdate(0)->showOnCreation(0)->showFilter(),
            shopwwiAmisFields(trans('field.user_id',[],'userPointLog'),'user_id')->showOnUpdate(0)->showOnCreation(0)->tableColumn(['sortable'=>true,'type'=>'tpl','tpl'=>'<span class=\'cxd-Tag\'>${user.nickname}(ID:${user_id})</span>'])
                ->filterColumn('picker',['joinValues'=>true,'source'=>shopwwiAdminUrl('users/list?_format=json'),'size'=>'lg','valueField'=>'id','labelField'=>'nickname','pickerSchema'=>AmisService::getUserList()]),
            shopwwiAmisFields(trans('field.operation_stage',[],'userPointLog'),'operation_stage')->showOnUpdate(0)->showOnCreation(0)->tableColumn(['sortable'=>true,'type'=>'mapping','map'=>$this->toMappingSelect($balanceOperationStage,'${operation_stage}','default')])
                ->filterColumn('select',['options'=>$balanceOperationStage])
                ->column('select',['options'=>$balanceOperationStage]),
            shopwwiAmisFields(trans('field.available_points',[],'userPointLog'),'available_points')->showOnUpdate(0)->showOnCreation(0)->tableColumn(['classNameExpr'=>"<%= data.available_points > 0 ? 'text-danger' : 'text-success' %>"]),
            shopwwiAmisFields(trans('field.frozen_points',[],'userPointLog'),'frozen_points')->showOnUpdate(0)->showOnCreation(0)->tableColumn(['classNameExpr'=>"<%= data.frozen_points > 0 ? 'text-danger' : 'text-success' %>"]),
            shopwwiAmisFields(trans('field.points',[],'userPointLog'),'points')->showOnUpdate(0)->showOnCreation(0)->tableColumn(['classNameExpr'=>'text-yellow-600']),
            shopwwiAmisFields(trans('field.description',[],'userPointLog'),'description')->showOnUpdate(0)->showOnCreation(0),
            shopwwiAmisFields(trans('field.sys_user_id',[],'userPointLog'),'sys_user_id')->showOnUpdate(0)->showOnCreation(0)->tableColumn(['sortable'=>true,'type'=>'tpl','tpl'=>'<span class=\'cxd-Tag\'>${sys_user_name}(ID:${sys_user_id})</span>'])->filterColumn('picker',['joinValues'=>true,'source'=>shopwwiAdminUrl('system/user/list?_format=json'),'size'=>'lg','valueField'=>'id','labelField'=>'nickname','pickerSchema'=>AmisService::getSysUserList()]),
            shopwwiAmisFields(trans('field.sys_user_name',[],'userPointLog'),'sys_user_name')->showOnUpdate(0)->showOnCreation(0)->showOnIndex(2),
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
     * 积分规则设置
     * @param Request $request
     * @return \support\Response
     */
    public function setting(Request $request)
    {
        try {
            if($request->method() === 'GET'){
                if($this->format() == 'json'){
                    $info = SysConfigService::getFirstOrCreate([
                        'key' => 'point'
                    ],['name'=>'积分规则设置','value'=>[
                        'used' => '1',
                        'rules' => [
                            ['label'=>'register','value'=>5,'desc'=>'该值为大于等于0的数，当会员注册成功后将获得相应的积分'],
                            ['label'=>'login','value'=>5,'desc'=>'该值为大于等于0的数，当会员每天第一次登录成功后将获得相应的积分']
                        ],
                    ]]);
                    return shopwwiSuccess($info);
                }
                $openOrClose = DictTypeService::getAmisDictType('openOrClose');
                $pointOrGrowthRuleType = DictTypeService::getAmisDictType('pointOrGrowthRuleType');
                $form = $this->baseForm()->body([
                    shopwwiAmis('grid')->gap('lg')->columns([
                        shopwwiAmis('radios')->name('point.used')->label(trans('config.used',[],$this->trans))->selectFirst(true)->options($openOrClose)->xs(12),
                        shopwwiAmis('combo')->name('point.rules')->label(trans('config.rules',[],$this->trans))->multiple(true)->draggable(true)->items([
                            shopwwiAmis('select')->name('label')->options($pointOrGrowthRuleType),
                            shopwwiAmis('input-text')->name('value'),
                            shopwwiAmis('input-text')->name('desc'),
                        ]),
                    ])
                ])->api('post:' . shopwwiAdminUrl('user/point/setting'))
                    ->actions([
                        shopwwiAmis('reset')->label(trans('reset',[],'messages')),
                        shopwwiAmis('submit')->label(trans('submit',[],'messages'))->level('primary')
                    ])
                    ->initApi(shopwwiAdminUrl('user/point/setting?_format=json'));
                $page = $this->basePage()->body($form);
                $page = $page->subTitle(trans('config.title',[],$this->trans));
                if($this->format() == 'data' || $this->format() == 'web') return shopwwiSuccess($page);
                return $this->getAdminView(['json'=>$page,'activeKey'=>'userPointSite']);
            }else{
                $params = shopwwiParams(['point']);
                SysConfigService::updateSetting($params);
            }
            return shopwwiSuccess();
        }catch (\Exception $e){
            return shopwwiError($e->getMessage());
        }
    }

}
