<?php
/**
 *-------------------------------------------------------------------------s*
 * 会员余额充值控制器
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

use Shopwwi\Admin\App\Admin\Service\AmisService;
use Shopwwi\Admin\App\Admin\Service\DictTypeService;
use Shopwwi\Admin\Libraries\Amis\AdminController;


class UserBalanceRechargeController extends AdminController
{
    protected $model = \Shopwwi\Admin\App\User\Models\UserBalanceRecharge::class;
    protected $orderBy = ['id' => 'desc'];
    protected $trans = 'userBalanceRecharge'; // 语言文件名称
    protected $queryPath = 'user/recharges'; // 完整路由地址
    protected $useHasCreate = false;
    protected $activeKey = 'userBalanceRechargeIndex';
    /**
     * 路由注册
     * @var string
     */
     public $routePath = 'recharges'; // 当前路由模块不填写则直接控制器名
    // public $routeAction = ['index'=>['GET','POST']]; //方法注册 未填写的则直接any
     public $routeNoAction = ['create','store']; //不允许方法注册
    // public $noNeedLogin = []; //不需要登入
    // public $noNeedAuth = []; //需要登入不需要鉴权
    /**
     * 数据字段处理
     * @return array
     */
    protected function fields(){
        $yesOrNo = DictTypeService::getAmisDictType('yesOrNo');
        return [
            shopwwiAmisFields(trans('field.id',[],'messages'),'id')->tableColumn(['width'=>60,'sortable'=>true])->showOnUpdate(0)->showOnCreation(0)->showFilter(),
            shopwwiAmisFields(trans('field.pay_sn',[],'userBalanceRecharge'),'pay_sn')->column('input-text',['static'=>true]),
            shopwwiAmisFields(trans('field.amount',[],'userBalanceRecharge'),'amount')->tableColumn(['classNameExpr'=>'text-yellow-600']),
            shopwwiAmisFields(trans('field.real_amount',[],'userBalanceRecharge'),'real_amount')->tableColumn(['classNameExpr'=>'text-success']),
            shopwwiAmisFields(trans('field.points',[],'userBalanceRecharge'),'points')->rules('required'),
            shopwwiAmisFields(trans('field.growth',[],'userBalanceRecharge'),'growth')->rules('required'),
            shopwwiAmisFields(trans('field.user_id',[],'userBalanceRecharge'),'user_id')->tableColumn(['sortable'=>true,'type'=>'tpl','tpl'=>'<span class=\'cxd-Tag\'>${user.nickname}(ID:${user_id})</span>'])
                ->filterColumn('picker',['joinValues'=>true,'source'=>shopwwiAdminUrl('users/list?_format=json'),'size'=>'lg','valueField'=>'id','labelField'=>'nickname','pickerSchema'=>AmisService::getUserList()]),
            shopwwiAmisFields(trans('field.meal_id',[],'userBalanceRecharge'),'meal_id')->column('input-number',['min'=>0]),
            shopwwiAmisFields(trans('field.status',[],'userBalanceRecharge'),'status')->filterColumn('select',['options'=>$yesOrNo])
                ->tableColumn(['sortable'=>true,'align'=>'center','type'=>'mapping','map'=>$this->toMappingSelect($yesOrNo,'${status}','default')])
                ->column('select',['selectFirst'=>true,'options'=>$yesOrNo]),
            shopwwiAmisFields(trans('field.sys_user_id',[],'userBalanceRecharge'),'sys_user_id')->showOnUpdate(0)->tableColumn(['toggled'=>false]),
            shopwwiAmisFields(trans('field.sys_user_name',[],'userBalanceRecharge'),'sys_user_name')->showOnUpdate(0)->tableColumn(['toggled'=>false]),
            shopwwiAmisFields(trans('field.created_at',[],'messages'),'created_at')->tableColumn(['width'=>145,'sortable'=>true])->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'created_at_betweenAmisTime'])->showOnUpdate(0)->showOnCreation(0),
            shopwwiAmisFields(trans('field.updated_at',[],'messages'),'updated_at')->tableColumn(['width'=>145,'toggled'=>false,'sortable'=>true])->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'updated_at_betweenAmisTime'])->showOnUpdate(0)->showOnCreation(0),
            shopwwiAmisFields(trans('field.deleted_at',[],'messages'),'deleted_at')->tableColumn(['width'=>145,'sortable'=>true])->showOnUpdate(0)->showOnCreation(0)->showOnIndex(4)->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'deleted_at_betweenAmisTime']),
        ];
    }

    protected function beforeJsonList($model){
        $model = $model->select($this->filterJsonFiled());
        $model->with(['user'=>function($q){
            $q->select('id','username','avatar','nickname');
        }]);
        return $model;
    }

}
