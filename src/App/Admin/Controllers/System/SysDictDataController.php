<?php
/**
 *-------------------------------------------------------------------------s*
 * 字典数据表
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

use Shopwwi\Admin\App\Admin\Service\DictTypeService;
use Shopwwi\Admin\Libraries\Amis\AdminController;

class SysDictDataController extends AdminController
{
    protected $model = \Shopwwi\Admin\App\Admin\Models\SysDictData::class;
    protected $trans = 'sysDictData'; // 语言文件名称
    protected $queryPath = 'system/dictdata'; // 完整路由地址
    protected $activeKey = 'settingSystemDict';
    public $routePath = 'dictdata'; // 当前路由模块不填写则直接控制器名
    protected $adminOp = true;
    protected $useIndexBack = true;
    protected $useHasRecovery = true;

    /**
     * 数据字段处理
     * @return array
     */
    protected function fields(){
        $openOrClose = DictTypeService::getAmisDictType('openOrClose');
        $yesOrNo = DictTypeService::getAmisDictType('yesOrNo');
        return [
            shopwwiAmisFields(trans('field.id',[],'sysDictData'),'id')->showOnCreation(0)->showOnUpdate(0)->tableColumn(['sortable'=>true]),
            shopwwiAmisFields(trans('field.label',[],'sysDictData'),'label')->rules('required'),
            shopwwiAmisFields(trans('field.value',[],'sysDictData'),'value')->rules('required')->tableColumn(['sortable'=>true]),
            shopwwiAmisFields(trans('field.type',[],'sysDictData'),'type')->rules('required')->showOnUpdate(4)->filterColumn(),
            shopwwiAmisFields(trans('field.is_default',[],'sysDictData'),'is_default')->rules(['bail','required','in:1,0'])->tableColumn(['sortable'=>true,'type'=>'mapping','map'=>$this->toMappingSelect($yesOrNo,'${allow_delete}')])
                ->filterColumn('select',['options'=>$yesOrNo])
                ->column('switch',['trueValue'=>1,'falseValue'=>0]),
            shopwwiAmisFields(trans('field.sort',[],'sysDictData'),'sort')->rules(['bail','required','numeric','min:0','max:999'])->column('input-number',['min'=>0,'max'=>999]),
            shopwwiAmisFields(trans('field.status',[],'sysDictData'),'status')->rules(['bail','required','in:1,0'])->tableColumn(['sortable'=>true,'type'=>'mapping','map'=>$this->toMappingSelect($openOrClose,'${status}')])->filterColumn('select',['options'=>$openOrClose])->column('switch',['trueValue'=>1,'falseValue'=>0]),
            shopwwiAmisFields(trans('field.allow_delete',[],'sysDictData'),'allow_delete')->rules(['bail','required','in:1,0'])->tableColumn(['sortable'=>true,'type'=>'mapping','map'=>$this->toMappingSelect($yesOrNo,'${allow_delete}')])
                ->filterColumn('select',['options'=>$yesOrNo])
                ->column('switch',['trueValue'=>1,'falseValue'=>0])->showOnUpdate(4),
            shopwwiAmisFields(trans('field.remark',[],'sysDictData'),'remark')->rules(['nullable','max:200'])->column('textarea',['md'=>12]),
            shopwwiAmisFields(trans('field.created_at',[],'sysDictData'),'created_at')->showOnUpdate(0)->showOnCreation(0)->tableColumn(['width'=>145,'sortable'=>true])->filterColumn('input-datetime-range',["format"=> "YYYY-MM-DD HH:mm:ss",'name'=>'created_at_betweenAmisTime']),
            shopwwiAmisFields(trans('field.updated_at',[],'sysDictData'),'updated_at')->showOnUpdate(0)->showOnCreation(0)->tableColumn(['width'=>145,'toggled'=>false,'sortable'=>true]),
            shopwwiAmisFields(trans('field.deleted_at',[],'messages'),'deleted_at')->tableColumn(['width'=>145,'sortable'=>true])->showOnUpdate(0)->showOnCreation(0)->showOnIndex(4)->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'deleted_at_betweenAmisTime'])
        ];
    }

    protected function getCreate(){
        return ['sort'=>999];
    }

}
