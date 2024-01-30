<?php
/**
 *-------------------------------------------------------------------------s*
 * 会员权益表控制器
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

use Shopwwi\Admin\Libraries\Amis\AdminController;


class UserGradeRightsController extends AdminController
{
    protected $model = \Shopwwi\Admin\App\User\Models\UserGradeRights::class;
    protected $orderBy = ['id' => 'desc'];
    protected $trans = 'userGradeRights'; // 语言文件名称
    protected $queryPath = 'user/grade/rights'; // 完整路由地址
    protected $activeKey = 'userGradeRights';
    /**
     * 路由注册
     * @var string
     */
     public $routePath = 'grade/rights'; // 当前路由模块不填写则直接控制器名
    // public $routeAction = ['index'=>['GET','POST']]; //方法注册 未填写的则直接any
     public $routeNoAction = ['recovery','restore','erasure']; //不允许方法注册
    // public $noNeedLogin = []; //不需要登入
    // public $noNeedAuth = []; //需要登入不需要鉴权
    /**
     * 数据字段处理
     * @return array
     */
    protected function fields(){
        return [
            shopwwiAmisFields(trans('field.id',[],'messages'),'id')->tableColumn(['width'=>60,'sortable'=>true])->showOnUpdate(0)->showOnCreation(0)->showFilter(),
            shopwwiAmisFields(trans('field.r_name',[],'userGradeRights'),'r_name')->rules('required'),
            shopwwiAmisFields(trans('field.r_key',[],'userGradeRights'),'r_key')->rules('required'),
            shopwwiAmisFields(trans('field.r_icon',[],'userGradeRights'),'r_icon')->rules('required'),
            shopwwiAmisFields(trans('field.r_remark',[],'userGradeRights'),'r_remark')->column('textarea',['md'=>12]),
            shopwwiAmisFields(trans('field.created_at',[],'messages'),'created_at')->tableColumn(['width'=>145,'sortable'=>true])->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'created_at_betweenAmisTime'])->showOnUpdate(0)->showOnCreation(0),
            shopwwiAmisFields(trans('field.updated_at',[],'messages'),'updated_at')->tableColumn(['width'=>145,'toggled'=>false,'sortable'=>true])->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'updated_at_betweenAmisTime'])->showOnUpdate(0)->showOnCreation(0),
        ];
    }

}
