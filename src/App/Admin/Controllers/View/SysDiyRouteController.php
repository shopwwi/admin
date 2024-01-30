<?php
/**
 *-------------------------------------------------------------------------s*
 * 页面路径控制器
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
namespace Shopwwi\Admin\App\Admin\Controllers\View;

use Shopwwi\Admin\Libraries\Amis\AdminController;


class SysDiyRouteController extends AdminController
{
    protected $model = \Shopwwi\Admin\App\Admin\Models\SysDiyRoute::class;
    protected $orderBy = ['id' => 'desc'];
    public function __construct()
    {
        $this->projectName = trans('projectName',[],'sysDiyRoute');
        $this->name = trans('name',[],'sysDiyRoute');
    }
    /**
     * 路由注册
     * @var string
     */
     public $routePath = 'route'; // 当前路由模块不填写则直接控制器名
    // public $routeAction = ['index'=>['GET','POST']]; //方法注册 未填写的则直接any
     public $routeNoAction = ['destroy','recovery','create','edit','store','update']; //不允许方法注册
    // public $noNeedLogin = []; //不需要登入
    // public $noNeedAuth = []; //需要登入不需要鉴权
    /**
     * 数据字段处理
     * @return array
     */
    protected function fields(){
        return [
            shopwwiAmisFields(trans('field.id',[],'sysDiyRoute'),'id')->showOnCreation(false)->showOnUpdate(false),
            shopwwiAmisFields(trans('field.name',[],'sysDiyRoute'),'name'),
            shopwwiAmisFields(trans('field.key',[],'sysDiyRoute'),'key'),
            shopwwiAmisFields(trans('field.group',[],'sysDiyRoute'),'group'),
            shopwwiAmisFields(trans('field.path',[],'sysDiyRoute'),'path'),
            shopwwiAmisFields(trans('field.client',[],'sysDiyRoute'),'client'),
            shopwwiAmisFields(trans('field.created_at',[],'sysDiyRoute'),'created_at')->showOnCreation(false)->showOnUpdate(false),
            shopwwiAmisFields(trans('field.updated_at',[],'sysDiyRoute'),'updated_at')->showOnCreation(false)->showOnUpdate(false),

        ];
    }

}
