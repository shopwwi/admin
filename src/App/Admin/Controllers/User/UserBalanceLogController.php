<?php
/**
 *-------------------------------------------------------------------------s*
 * 会员余额变动控制器
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
use Shopwwi\Admin\Libraries\Amis\AdminController;


class UserBalanceLogController extends AdminController
{
    protected $model = \Shopwwi\Admin\App\User\Models\UserBalanceLog::class;
    protected $orderBy = ['id' => 'desc'];
    protected $trans = 'userBalanceLog'; // 语言文件名称
    protected $queryPath = 'user/balance'; // 完整路由地址
    protected $activeKey = 'userBalanceLog';
    protected $useHasCreate = false;

    /**
     * 路由注册
     * @var string
     */
     public $routePath = 'balance'; // 当前路由模块不填写则直接控制器名
    // public $routeAction = ['index'=>['GET','POST']]; //方法注册 未填写的则直接any
    public $routeNoAction = ['create','store','update','edit','destroy','recovery','erasure','restore']; //不允许方法注册
    // public $noNeedLogin = []; //不需要登入
    // public $noNeedAuth = []; //需要登入不需要鉴权
    /**
     * 数据字段处理
     * @return array
     */
    protected function fields(){
        $balanceOperationStage = DictTypeService::getAmisDictType('balanceOperationStage');
        return [
            shopwwiAmisFields(trans('field.id',[],'messages'),'id')->tableColumn(['width'=>60,'sortable'=>true])->showOnUpdate(0)->showOnCreation(0)->showFilter(),
            shopwwiAmisFields(trans('field.user_id',[],'userBalanceLog'),'user_id')->showOnUpdate(0)->showOnCreation(0)->tableColumn(['sortable'=>true,'type'=>'tpl','tpl'=>'<span class=\'cxd-Tag\'>${user.nickname}(ID:${user_id})</span>'])
                ->filterColumn('picker',['joinValues'=>true,'source'=>shopwwiAdminUrl('users/list?_format=json'),'size'=>'lg','valueField'=>'id','labelField'=>'nickname','pickerSchema'=>AmisService::getUserList()]),
            shopwwiAmisFields(trans('field.operation_stage',[],'userBalanceLog'),'operation_stage')->showOnUpdate(0)->showOnCreation(0)->tableColumn(['sortable'=>true,'type'=>'mapping','map'=>$this->toMappingSelect($balanceOperationStage,'${operation_stage}','default')])
                ->filterColumn('select',['options'=>$balanceOperationStage])
                ->column('select',['options'=>$balanceOperationStage]),
            shopwwiAmisFields(trans('field.available_balance',[],'userBalanceLog'),'available_balance')->showOnUpdate(0)->showOnCreation(0)->tableColumn(['classNameExpr'=>"<%= data.available_balance > 0 ? 'text-danger' : 'text-success' %>"]),
            shopwwiAmisFields(trans('field.frozen_balance',[],'userBalanceLog'),'frozen_balance')->showOnUpdate(0)->showOnCreation(0)->tableColumn(['classNameExpr'=>"<%= data.frozen_balance > 0 ? 'text-danger' : 'text-success' %>"]),
            shopwwiAmisFields(trans('field.old_amount',[],'userBalanceLog'),'old_amount')->showOnUpdate(0)->showOnCreation(0)->tableColumn(['classNameExpr'=>'text-yellow-600']),
            shopwwiAmisFields(trans('field.description',[],'userBalanceLog'),'description')->showOnUpdate(0)->showOnCreation(0),
            shopwwiAmisFields(trans('field.sys_user_id',[],'userBalanceLog'),'sys_user_id')->showOnUpdate(0)->showOnCreation(0)->tableColumn(['sortable'=>true,'type'=>'tpl','tpl'=>'<span class=\'cxd-Tag\'>${sys_user_name}(ID:${sys_user_id})</span>'])->filterColumn('picker',['joinValues'=>true,'source'=>shopwwiAdminUrl('system/user/list?_format=json'),'size'=>'lg','valueField'=>'id','labelField'=>'nickname','pickerSchema'=>AmisService::getSysUserList()]),
            shopwwiAmisFields(trans('field.sys_user_name',[],'userBalanceLog'),'sys_user_name')->showOnUpdate(0)->showOnCreation(0)->showOnIndex(2),
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
            $this->rowShowButton(1)
        ]);
    }
}
