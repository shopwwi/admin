<?php
/**
 *-------------------------------------------------------------------------s*
 * 操作日志控制器
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

use Shopwwi\Admin\Amis\Operation;
use Shopwwi\Admin\App\Admin\Service\DictTypeService;
use Shopwwi\Admin\Libraries\Amis\AdminController;

class SysOperLogController extends AdminController
{
    protected $model = \Shopwwi\Admin\App\Admin\Models\SysOperLog::class;
    protected $orderBy = ['id'=>'desc'];

    protected $trans = 'sysOperLog'; // 语言文件名称
    protected $queryPath = 'system/log'; // 完整路由地址
    protected $activeKey = 'settingSystemPowerLog';
    protected $useHasCreate = false;

    public $routePath = 'log'; // 当前路由模块不填写则直接控制器名
    public $routeNoAction = ['create','update','edit','update','recovery','restore','erasure']; //不允许注册路由

    /**
     * 数据字段处理
     * @return array
     */
    protected function fields(){
        $successOrError = DictTypeService::getAmisDictType('successOrError');
        $businessType = DictTypeService::getAmisDictType('businessType');
        return [
            shopwwiAmisFields(trans('field.id',[],'messages'),'id')->tableColumn(['width'=>60,'sortable'=>true])->showOnUpdate(0)->showOnCreation(0)->showFilter(),
            shopwwiAmisFields(trans('field.title',[],'sysOperLog'),'title'),
            shopwwiAmisFields(trans('field.business_type',[],'sysOperLog'),'business_type')->filterColumn('select',['options'=>$businessType])->tableColumn(['sortable'=>true,'type'=>'mapping','map'=>$this->toMappingSelect($businessType,'${business_type}','text')]),
            shopwwiAmisFields(trans('field.method',[],'sysOperLog'),'method'),
            shopwwiAmisFields(trans('field.request_method',[],'sysOperLog'),'request_method'),
            shopwwiAmisFields(trans('field.type',[],'sysOperLog'),'type'),
            shopwwiAmisFields(trans('field.name',[],'sysOperLog'),'name'),
            shopwwiAmisFields(trans('field.url',[],'sysOperLog'),'url')->tableColumn(['toggled'=>false]),
            shopwwiAmisFields(trans('field.ip',[],'sysOperLog'),'ip'),
            shopwwiAmisFields(trans('field.location',[],'sysOperLog'),'location'),
            shopwwiAmisFields(trans('field.param',[],'sysOperLog'),'param')->showOnIndex(2)->showColumn('editor',['language'=>'json','md'=>12,'disabled'=>true]),
            shopwwiAmisFields(trans('field.json_result',[],'sysOperLog'),'json_result')->showOnIndex(2)->showColumn('editor',['language'=>'json','md'=>12,'disabled'=>true]),
            shopwwiAmisFields(trans('field.status',[],'sysOperLog'),'status')->filterColumn('select',['options'=>$successOrError])->tableColumn(['sortable'=>true,'type'=>'mapping','map'=>$this->toMappingSelect($successOrError,'${status}','text')]),
            shopwwiAmisFields(trans('field.error_msg',[],'sysOperLog'),'error_msg')->showOnIndex(2)->showColumn('editor',['language'=>'json','md'=>12,'disabled'=>true]),
            shopwwiAmisFields(trans('field.created_at',[],'messages'),'created_at')->tableColumn(['width'=>145,'sortable'=>true])->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'created_at_betweenAmisTime'])->showOnUpdate(0)->showOnCreation(0),
            shopwwiAmisFields(trans('field.updated_at',[],'messages'),'updated_at')->tableColumn(['width'=>145,'toggled'=>false,'sortable'=>true])->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'updated_at_betweenAmisTime'])->showOnUpdate(0)->showOnCreation(0),
        ];
    }

    protected function operation()
    {
        return Operation::make()->label(trans('actions',[],'messages'))->fixed('right')->width(160)->buttons([
            $this->rowShowButton(1),
            $this->rowDeleteButton(),
        ]);
    }

}
