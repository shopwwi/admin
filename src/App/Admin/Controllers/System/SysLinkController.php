<?php
/**
 *-------------------------------------------------------------------------s*
 * 友情链接控制器
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
use Shopwwi\Admin\Libraries\Amis\AdminController;


class SysLinkController extends AdminController
{
    protected $model = \Shopwwi\Admin\App\Admin\Models\SysLink::class;
    protected $orderBy = ['id' => 'desc'];
    protected $adminOp = true;
    protected $useHasRecovery = true;
    protected $trans = 'sysLink'; // 语言文件名称
    protected $queryPath = 'system/link'; // 完整路由地址
    protected $activeKey = 'settingContentLink';

    /**
     * 路由注册
     * @var string
     */
     public $routePath = 'link'; // 当前路由模块不填写则直接控制器名
    // public $routeAction = ['index'=>['GET','POST']]; //方法注册 未填写的则直接any
    // public $routeNoAction = ['index']; //不允许方法注册
    // public $noNeedLogin = []; //不需要登入
    // public $noNeedAuth = []; //需要登入不需要鉴权
    /**
     * 数据字段处理
     * @return array
     */
    protected function fields(){
        $verifyStatus = DictTypeService::getAmisDictType('verifyStatus');
        $yesOrNo = DictTypeService::getAmisDictType('yesOrNo');
        return [
            shopwwiAmisFields(trans('field.id',[],'messages'),'id')->tableColumn(['width'=>60,'sortable'=>true])->showOnUpdate(0)->showOnCreation(0)->showFilter(),
            shopwwiAmisFields(trans('field.name',[],'sysLink'),'name')->rules('required'),
            shopwwiAmisFields(trans('field.url',[],'sysLink'),'url'),
            shopwwiAmisFields(trans('field.image',[],'sysLink'),'image')->column('hidden',['md'=>12])->showOnIndex(2),
            shopwwiAmisFields(trans('field.image',[],'sysLink'),'imageUrl')->column('input-image',['md'=>12,'autoFill'=>['image'=>'${file_name}'],'initAutoFill'=>false,'crop'=>['aspectRatio'=>3],'receiver'=>shopwwiAdminUrl('common/upload')])->showColumn('control',['body'=>['type'=>'image','name'=>'imageUrl','width'=>90,'height'=>90]])->showOnIndex(0)->showOnCreation(3)->showOnUpdate(3),
            shopwwiAmisFields(trans('field.sort',[],'messages'),'sort')->tableColumn(['width'=>60,'sortable'=>true])->rules(['bail','required','numeric','min:0','max:999'])->column('input-number',['min'=>0,'max'=>999]),
            shopwwiAmisFields(trans('field.status',[],'sysLink'),'status')->rules('required')->tableColumn(['sortable'=>true,'type'=>'mapping','map'=>$this->toMappingSelect($verifyStatus,'${status}')])->filterColumn('select',['options'=>$verifyStatus])->column('radios',['options'=>$verifyStatus,'selectFirst'=>true]),
            shopwwiAmisFields(trans('field.is_blank',[],'sysLink'),'is_blank')->rules('required')->tableColumn(['sortable'=>true,'type'=>'mapping','map'=>$this->toMappingSelect($yesOrNo,'${allow_delete}')])
                ->filterColumn('select',['options'=>$yesOrNo])
                ->column('switch',['trueValue'=>1,'falseValue'=>0]),
            shopwwiAmisFields(trans('field.app',[],'sysLink'),'app'),
            shopwwiAmisFields(trans('field.created_user_id',[],'messages'),'created_user_id')->showOnUpdate(0)->showOnCreation(0)->tableColumn(['width'=>60,'toggled'=>false,'sortable'=>true])->filterColumn('picker',['joinValues'=>true,'source'=>shopwwiAdminUrl('system/user/list?_format=json'),'size'=>'lg','valueField'=>'id','labelField'=>'nickname','pickerSchema'=>AmisService::getSysUserList()]),
            shopwwiAmisFields(trans('field.created_at',[],'messages'),'created_at')->tableColumn(['width'=>145,'sortable'=>true])->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'created_at_betweenAmisTime'])->showOnUpdate(0)->showOnCreation(0),
            shopwwiAmisFields(trans('field.updated_user_id',[],'messages'),'updated_user_id')->showOnUpdate(0)->showOnCreation(0)->tableColumn(['width'=>60,'toggled'=>false,'sortable'=>true])->filterColumn('picker',['joinValues'=>true,'source'=>shopwwiAdminUrl('system/user/list?_format=json'),'size'=>'lg','valueField'=>'id','labelField'=>'nickname','pickerSchema'=>AmisService::getSysUserList()]),
            shopwwiAmisFields(trans('field.updated_at',[],'messages'),'updated_at')->tableColumn(['width'=>145,'toggled'=>false,'sortable'=>true])->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'updated_at_betweenAmisTime'])->showOnUpdate(0)->showOnCreation(0),
            shopwwiAmisFields(trans('field.deleted_at',[],'messages'),'deleted_at')->tableColumn(['width'=>145,'sortable'=>true])->showOnUpdate(0)->showOnCreation(0)->showOnIndex(4)->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'deleted_at_betweenAmisTime'])
        ];
    }
    protected function getCreate(){
        return ['is_blank' => '0','status' => 1,'sort'=>999];
    }

}
