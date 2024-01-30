<?php
/**
 *-------------------------------------------------------------------------s*
 * 物流公司控制器
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
namespace Shopwwi\Admin\App\Admin\Controllers\System;

use Shopwwi\Admin\App\Admin\Service\AmisService;
use Shopwwi\Admin\App\Admin\Service\DictTypeService;
use Shopwwi\Admin\App\Admin\Service\SysConfigService;
use Shopwwi\Admin\Libraries\Amis\AdminController;
use support\Request;


class SysShipCompanyController extends AdminController
{
    public $model = \Shopwwi\Admin\App\Admin\Models\SysShipCompany::class;
    public  $orderBy = ['id' => 'desc'];
    protected $adminOp = true;

    protected $trans = 'sysShipCompany'; // 语言文件名称
    protected $queryPath = 'system/ship'; // 完整路由地址
    protected $useHasRecovery = true;
    protected $activeKey = 'settingSiteShip';
    /**
     * 路由注册
     * @var string
     */
     public $routePath = 'ship'; // 当前路由模块不填写则直接控制器名
    // public $routeAction = ['index'=>['GET','POST']]; //方法注册 未填写的则直接any
    // public $routeNoAction = ['index']; //不允许方法注册
    // public $noNeedLogin = []; //不需要登入
    // public $noNeedAuth = []; //需要登入不需要鉴权
    /**
     * 数据字段处理
     * @return array
     */
    protected function fields(){
        $openOrClose = DictTypeService::getAmisDictType('openOrClose');
        $yesOrNo = DictTypeService::getAmisDictType('yesOrNo');
        return [
            shopwwiAmisFields(trans('field.id',[],'messages'),'id')->tableColumn(['width'=>60,'sortable'=>true])->showOnUpdate(0)->showOnCreation(0)->showFilter(),
            shopwwiAmisFields(trans('field.name',[],'sysShipCompany'),'name')->rules('required'),
            shopwwiAmisFields(trans('field.code',[],'sysShipCompany'),'code')->rules('required'),
            shopwwiAmisFields(trans('field.letter',[],'sysShipCompany'),'letter'),
            shopwwiAmisFields(trans('field.status',[],'sysShipCompany'),'status')->filterColumn('select',['options'=>$openOrClose])->tableColumn(['quickEdit' => shopwwiAmis('switch')->trueValue(1)->falseValue(0)->mode('inline')
                ->saveImmediately(true)])->column('radios',['selectFirst'=>true,'options'=>$openOrClose])
                ->rules('required','in:1,0'),
            shopwwiAmisFields(trans('field.is_default',[],'sysShipCompany'),'is_default')->filterColumn('select',['options'=>$yesOrNo])->tableColumn(['quickEdit' => shopwwiAmis('switch')->trueValue(1)->falseValue(0)->mode('inline')
                ->saveImmediately(true)])->column('radios',['selectFirst'=>true,'options'=>$yesOrNo])
                ->rules('required','in:1,0'),
            shopwwiAmisFields(trans('field.url',[],'sysShipCompany'),'url'),
            shopwwiAmisFields(trans('field.created_user_id',[],'messages'),'created_user_id')->showOnUpdate(0)->showOnCreation(0)->tableColumn(['width'=>60,'toggled'=>false,'sortable'=>true])->filterColumn('picker',['joinValues'=>true,'source'=>shopwwiAdminUrl('system/user/list?_format=json'),'size'=>'lg','valueField'=>'id','labelField'=>'nickname','pickerSchema'=>AmisService::getSysUserList()]),
            shopwwiAmisFields(trans('field.created_at',[],'messages'),'created_at')->tableColumn(['width'=>145,'sortable'=>true])->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'created_at_betweenAmisTime'])->showOnUpdate(0)->showOnCreation(0),
            shopwwiAmisFields(trans('field.updated_user_id',[],'messages'),'updated_user_id')->showOnUpdate(0)->showOnCreation(0)->tableColumn(['width'=>60,'toggled'=>false,'sortable'=>true])->filterColumn('picker',['joinValues'=>true,'source'=>shopwwiAdminUrl('system/user/list?_format=json'),'size'=>'lg','valueField'=>'id','labelField'=>'nickname','pickerSchema'=>AmisService::getSysUserList()]),
            shopwwiAmisFields(trans('field.updated_at',[],'messages'),'updated_at')->tableColumn(['width'=>145,'toggled'=>false,'sortable'=>true])->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'updated_at_betweenAmisTime'])->showOnUpdate(0)->showOnCreation(0),
            shopwwiAmisFields(trans('field.deleted_at',[],'messages'),'deleted_at')->tableColumn(['width'=>145,'sortable'=>true])->showOnUpdate(0)->showOnCreation(0)->showOnIndex(4)->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'deleted_at_betweenAmisTime']),
        ];
    }

    /**
     * 物流接口设置
     * @param Request $request
     * @return \support\Response
     */
    public function setting(Request $request)
    {
        try {
            if($request->method() === 'GET'){
                if($this->format() == 'json'){
                    $info = SysConfigService::getFirstOrCreate([
                        'key' => 'express'
                    ],['name'=>'快递查询','value'=>[
                        'used' => '0',
                        'type' => 'kdniao',
                        'appId' => '',
                        'appKey' => '',
                    ]]);
                    return shopwwiSuccess($info);
                }
                $expressType = DictTypeService::getAmisDictType('expressType');
                $openOrClose = DictTypeService::getAmisDictType('openOrClose');
                $form = $this->baseForm()->body([
                   shopwwiAmis('grid')->gap('lg')->columns([
                       shopwwiAmis('select')->name('express.type')->label(trans('config.type',[],$this->trans))->options($expressType)->placeholder(trans('form.select',['attribute'=>trans('config.type',[],$this->trans)],'messages'))->xs(12),
                       shopwwiAmis('input-text')->name('express.appId')->label(trans('config.appId',[],$this->trans))->placeholder(trans('form.input',['attribute'=>trans('config.appId',[],$this->trans)],'messages'))->xs(12),
                       shopwwiAmis('input-text')->name('express.appKey')->label(trans('config.appKey',[],$this->trans))->placeholder(trans('form.input',['attribute'=>trans('config.appKey',[],$this->trans)],'messages'))->xs(12),
                       shopwwiAmis('radios')->name('express.used')->label(trans('config.used',[],$this->trans))->selectFirst(true)->options($openOrClose)->xs(12)
                   ])
                ])->api('post:' . shopwwiAdminUrl('system/ship/setting'))
                    ->actions([
                        shopwwiAmis('reset')->label(trans('reset',[],'messages')),
                        shopwwiAmis('submit')->label(trans('submit',[],'messages'))->level('primary')
                    ])
                    ->initApi(shopwwiAdminUrl('system/ship/setting?_format=json'));
                $page = $this->basePage()->body($form);
                $page = $page->subTitle(trans('config.title',[],$this->trans));
                if($this->format() == 'data' || $this->format() == 'web') return shopwwiSuccess($page);
                return $this->getAdminView(['json'=>$page,'activeKey'=>'settingSiteShipSite']);
            }else{
                $params = shopwwiParams(['express']);
                SysConfigService::updateSetting($params);
            }
            return shopwwiSuccess();
        }catch (\Exception $e){
            return shopwwiError($e->getMessage());
        }
    }

}
