<?php
/**
 *-------------------------------------------------------------------------s*
 * 移动端装修页面控制器
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

use Shopwwi\Admin\App\Admin\Service\AmisService;
use Shopwwi\Admin\Libraries\Amis\AdminController;
use support\Request;
use support\Response;

class SysDiyMobileController extends AdminController
{
    protected $model = \Shopwwi\Admin\App\Admin\Models\SysDiyMobile::class;
    protected $orderBy = ['id' => 'desc'];
    protected $adminOp = true;
    public function __construct()
    {
        $this->projectName = trans('projectName',[],'sysDiyMobile');
        $this->name = trans('name',[],'sysDiyMobile');
    }
    /**
     * 路由注册
     * @var string
     */
     public $routePath = 'diy'; // 当前路由模块不填写则直接控制器名
    // public $routeAction = ['index'=>['GET','POST']]; //方法注册 未填写的则直接any
    // public $routeNoAction = ['index']; //不允许方法注册
    // public $noNeedLogin = []; //不需要登入
    // public $noNeedAuth = []; //需要登入不需要鉴权
    /**
     * 数据字段处理
     * @return array
     */
    protected function fields(){
        return [
            shopwwiAmisFields(trans('field.id',[],'sysDiyMobile'),'id')->showOnCreation(false)->showOnUpdate(false),
            shopwwiAmisFields(trans('field.name',[],'sysDiyMobile'),'name')->rules('required'),
            shopwwiAmisFields(trans('field.key',[],'sysDiyMobile'),'key')->showOnCreation(false)->showOnUpdate(false),
            shopwwiAmisFields(trans('field.click_num',[],'sysDiyMobile'),'click_num')->showOnCreation(false)->showOnUpdate(false),
            shopwwiAmisFields(trans('field.sort',[],'sysDiyMobile'),'sort'),
            shopwwiAmisFields(trans('field.global',[],'sysDiyMobile'),'global')->rules('required'),
            shopwwiAmisFields(trans('field.items',[],'sysDiyMobile'),'items')->rules('required'),
            shopwwiAmisFields(trans('field.created_user_id',[],'sysDiyMobile'),'created_user_id')->showOnCreation(false)->showOnUpdate(false)->filterColumn('picker',['joinValues'=>true,'source'=>shopwwiAdminUrl('system/user/list?_format=json'),'size'=>'lg','valueField'=>'id','labelField'=>'nickname','pickerSchema'=>AmisService::getSysUserList()]),
            shopwwiAmisFields(trans('field.created_at',[],'sysDiyMobile'),'created_at')->showOnCreation(false)->showOnUpdate(false),
            shopwwiAmisFields(trans('field.updated_user_id',[],'sysDiyMobile'),'updated_user_id')->showOnCreation(false)->showOnUpdate(false)->filterColumn('picker',['joinValues'=>true,'source'=>shopwwiAdminUrl('system/user/list?_format=json'),'size'=>'lg','valueField'=>'id','labelField'=>'nickname','pickerSchema'=>AmisService::getSysUserList()]),
            shopwwiAmisFields(trans('field.updated_at',[],'sysDiyMobile'),'updated_at')->showOnCreation(false)->showOnUpdate(false),
        ];
    }

    /**
     * 获取用户中心模板
     * @param Request $request
     * @return Response
     */
    public function user(Request $request)
    {
        try {
            $model = new $this->model;
            $info = $model->firstOrCreate([
                    'key'=>'VIEW_DIY_USER'
                ],[
                    'global'=>[
                        'page' => ['name'=>'会员中心','template'=>'default','image'=>[],'header'=>true,'align'=>'center','url'=>[]],
                        'footer' => ['tabBar' => true],
                        'styles' =>[
                            'global' =>['backgroundColor'=>'rgba(255,255,255,1)','headerColor'=>'#FFFFFF','headerTitleColor' => '#333333','headerTransparent' => false,'backgroundImage'=>[]],
                            'padding' => ['x'=>12]
                        ]
                    ],
                    'items' => []
                ]
            );
            return shopwwiSuccess(['info'=>$info]);
        }catch (\Exception $e){
            return shopwwiError($e->getMessage());
        }
    }

}
