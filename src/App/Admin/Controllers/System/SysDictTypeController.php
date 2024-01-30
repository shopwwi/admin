<?php
/**
 *-------------------------------------------------------------------------s*
 * 字典类型表
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


class SysDictTypeController extends AdminController
{
    protected $model = \Shopwwi\Admin\App\Admin\Models\SysDictType::class;
    protected $trans = 'sysDictType'; // 语言文件名称
    protected $queryPath = 'system/dict'; // 完整路由地址
    protected $activeKey = 'settingSystemDict';
    protected $adminOp = true;
    protected $useHasRecovery = true;
    public $routePath = 'dict'; // 当前路由模块不填写则直接控制器名
    /**
     * 数据字段处理
     * @return array
     */
    protected function fields(){
        $openOrClose = DictTypeService::getAmisDictType('openOrClose');
        $yesOrNo = DictTypeService::getAmisDictType('yesOrNo');
        return [
            shopwwiAmisFields(trans('field.id',[],'sysDictType'),'id')->tableColumn(['width'=>60,'sortable'=>true])->showOnCreation(0)->showOnUpdate(0)->showFilter(),
            shopwwiAmisFields(trans('field.type',[],'sysDictType'),'type')->tableColumn(['sortable'=>true,'copyable'=>true,'type'=>'link','href'=>$this->getUrl('system/dictdata?type=${type}'),'body'=>'${type}','blank'=>false])->rules('required')->updateColumn('input-text',['static'=>true])->showOnUpdate(4),
            shopwwiAmisFields(trans('field.name',[],'sysDictType'),'name')->rules(['bail','required','min:2','max:25'])->filterColumn('input-text',['name'=>'name_like']),
            shopwwiAmisFields(trans('field.status',[],'sysDictType'),'status')
                ->tableColumn(['sortable'=>true,'type'=>'mapping','map'=>$this->toMappingSelect($openOrClose,'${status}')])->filterColumn('select',['options'=>$openOrClose])->column('switch',['trueValue'=>1,'falseValue'=>0]),
            shopwwiAmisFields(trans('field.allow_delete',[],'sysDictType'),'allow_delete')
                ->tableColumn(['sortable'=>true,'type'=>'mapping','map'=>$this->toMappingSelect($yesOrNo,'${allow_delete}')])
                ->filterColumn('select',['options'=>$yesOrNo])
                ->column('switch',['trueValue'=>1,'falseValue'=>0])->showOnUpdate(4),
            shopwwiAmisFields(trans('field.remark',[],'sysDictType'),'remark')->column('textarea',['md'=>12]),
            shopwwiAmisFields(trans('field.created_at',[],'messages'),'created_at')->tableColumn(['width'=>145,'sortable'=>true])->filterColumn('input-datetime-range',["format"=> "YYYY-MM-DD HH:mm:ss",'name'=>'created_at_betweenAmisTime'])->showOnUpdate(0)->showOnCreation(0),
            shopwwiAmisFields(trans('field.updated_at',[],'messages'),'updated_at')->tableColumn(['width'=>145,'toggled'=>false,'sortable'=>true])->showOnUpdate(0)->showOnCreation(0),
            shopwwiAmisFields(trans('field.deleted_at',[],'messages'),'deleted_at')->tableColumn(['width'=>145,'sortable'=>true])->showOnUpdate(0)->showOnCreation(0)->showOnIndex(4)
        ];
    }
//
//    public function dicts(SysDictType $dictType)
//    {
//        $dicts = $dictType->dicts();
//
//        return shopwwiSuccess($dicts);
//    }
}
