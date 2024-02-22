<?php
/**
 *-------------------------------------------------------------------------s*
 * 系统公告控制器
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


class SysNoticeController extends AdminController
{
    protected $model = \Shopwwi\Admin\App\Admin\Models\SysNotice::class;
    protected $orderBy = ['id' => 'desc'];
    protected $activeKey = 'settingContentNotice';
    protected $trans = 'sysNotice'; // 语言文件名称
    protected $queryPath = 'system/notice'; // 完整路由地址
    protected $adminOp = true;
    protected $buttonCache = true;
    protected $useCreateDialog = 0;
    protected $useEditDialog = 0;
    protected $useShowDialog = 2;
    protected $useEditDialogSize = 'lg';
    protected $useCreateDialogSize = 'lg';
    protected $useShowDialogSize = 'lg';
    /**
     * 路由注册
     * @var string
     */
     public $routePath = 'notice'; // 当前路由模块不填写则直接控制器名
    // public $routeAction = ['index'=>['GET','POST']]; //方法注册 未填写的则直接any
     public $routeNoAction = ['recovery']; //不允许方法注册
    // public $noNeedLogin = []; //不需要登入
    // public $noNeedAuth = []; //需要登入不需要鉴权
    /**
     * 数据字段处理
     * @return array
     */
    protected function fields(){
        $sysNoticePosition = DictTypeService::getAmisDictType('sysNoticePosition');
        $yesOrNo = DictTypeService::getAmisDictType('yesOrNo');
        return [
            shopwwiAmisFields(trans('field.id',[],'messages'),'id')->tableColumn(['width'=>60,'sortable'=>true])->showOnUpdate(0)->showOnCreation(0)->showFilter(),
            shopwwiAmisFields(trans('field.title',[],'sysNotice'),'title')->rules(['bail','required']),
            shopwwiAmisFields(trans('field.position',[],'sysNotice'),'position')->tableColumn(['sortable'=>true,'type'=>'mapping','map'=>$this->toMappingSelect($sysNoticePosition,'${position}','default')])
                ->filterColumn('select',['options'=>$sysNoticePosition])
                ->column('select',['options'=>$sysNoticePosition])->rules(['bail','required','numeric','min:0']),
            shopwwiAmisFields(trans('field.content',[],'sysNotice'),'content')->showOnIndex(0)->column('input-rich-text',['sm'=>12])->showColumn('control',['body'=>['type'=>'tpl','tpl'=>'${content|raw}','sm'=>12]]),
            shopwwiAmisFields(trans('field.is_top',[],'sysNotice'),'is_top')->tableColumn(['sortable'=>true,'type'=>'mapping','map'=>$this->toMappingSelect($yesOrNo,'${is_top}')])
                ->filterColumn('select',['options'=>$yesOrNo])
                ->column('switch',['trueValue'=>1,'falseValue'=>0])->rules(['required','numeric','in:1,0']),
            shopwwiAmisFields(trans('field.sort',[],'messages'),'sort')->tableColumn(['width'=>60,'sortable'=>true])->rules(['bail','required','numeric','min:0','max:999'])->column('input-number',['min'=>0,'max'=>999]),
            shopwwiAmisFields(trans('field.created_user_id',[],'messages'),'created_user_id')->showOnUpdate(0)->showOnCreation(0)->tableColumn(['width'=>60,'toggled'=>false,'sortable'=>true])->filterColumn('picker',['joinValues'=>true,'source'=>shopwwiAdminUrl('system/user/list?_format=json'),'size'=>'lg','valueField'=>'id','labelField'=>'nickname','pickerSchema'=>AmisService::getSysUserList()]),
            shopwwiAmisFields(trans('field.created_at',[],'messages'),'created_at')->tableColumn(['width'=>145,'sortable'=>true])->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'created_at_betweenAmisTime'])->showOnUpdate(0)->showOnCreation(0),
            shopwwiAmisFields(trans('field.updated_user_id',[],'messages'),'updated_user_id')->showOnUpdate(0)->showOnCreation(0)->tableColumn(['width'=>60,'toggled'=>false,'sortable'=>true])->filterColumn('picker',['joinValues'=>true,'source'=>shopwwiAdminUrl('system/user/list?_format=json'),'size'=>'lg','valueField'=>'id','labelField'=>'nickname','pickerSchema'=>AmisService::getSysUserList()]),
            shopwwiAmisFields(trans('field.updated_at',[],'messages'),'updated_at')->tableColumn(['width'=>145,'toggled'=>false,'sortable'=>true])->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'updated_at_betweenAmisTime'])->showOnUpdate(0)->showOnCreation(0),
        ];
    }
    protected function getCreate()
    {
        return ['sort'=>999,'is_top'=>0];
    }

}
