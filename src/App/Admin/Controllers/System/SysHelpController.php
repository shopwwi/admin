<?php
/**
 *-------------------------------------------------------------------------s*
 * 帮助主题控制器
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

use Shopwwi\Admin\App\Admin\Models\SysHelpClass;
use Shopwwi\Admin\App\Admin\Service\AmisService;
use Shopwwi\Admin\Libraries\Amis\AdminController;


class SysHelpController extends AdminController
{
    protected $model = \Shopwwi\Admin\App\Admin\Models\SysHelp::class;
    protected $orderBy = ['id' => 'desc'];
    protected $adminOp = true;
    protected $trans = 'sysHelp'; // 语言文件名称
    protected $queryPath = 'system/helps'; // 完整路由地址
    protected $activeKey = 'settingContentHelpIndex';
    protected $useHasRecovery = true;
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
     public $routePath = 'helps'; // 当前路由模块不填写则直接控制器名
    // public $routeAction = ['index'=>['GET','POST']]; //方法注册 未填写的则直接any
//     public $routeNoAction = ['recovery','restore','erasure']; //不允许方法注册
    // public $noNeedLogin = []; //不需要登入
    // public $noNeedAuth = []; //需要登入不需要鉴权
    /**
     * 数据字段处理
     * @return array
     */
    protected function fields(){
        return [
            shopwwiAmisFields(trans('field.id',[],'messages'),'id')->tableColumn(['width'=>60,'sortable'=>true])->showOnUpdate(0)->showOnCreation(0)->showFilter(),
            shopwwiAmisFields(trans('field.title',[],'sysHelp'),'title')->rules(['required','min:3','max:70']),
            shopwwiAmisFields(trans('field.class_id',[],'sysHelp'),'class_id')->rules(['required','numeric','min:1'])->column('select',['source'=>'$classList','labelField'=>'name','valueField'=>'id'])->tableColumn(['type'=>'link','href'=>shopwwiAdminUrl('system/help/class?id=${class_id}'),'body'=>'${class.name}[${class.id}]']),
            shopwwiAmisFields(trans('field.content',[],'sysHelp'),'content')->showOnIndex(0)->column('input-rich-text',['md'=>12])->showColumn('control',['body'=>['type'=>'tpl','tpl'=>'${content|raw}','md'=>12]]),
            shopwwiAmisFields(trans('field.sort',[],'messages'),'sort')->tableColumn(['width'=>60,'sortable'=>true])->rules(['bail','required','numeric','min:0','max:999'])->column('input-number',['min'=>0,'max'=>999]),
            shopwwiAmisFields(trans('field.url',[],'sysHelp'),'url'),
            shopwwiAmisFields(trans('field.created_user_id',[],'messages'),'created_user_id')->showOnUpdate(0)->showOnCreation(0)->tableColumn(['width'=>60,'toggled'=>false,'sortable'=>true])->filterColumn('picker',['joinValues'=>true,'source'=>shopwwiAdminUrl('system/user/list?_format=json'),'size'=>'lg','valueField'=>'id','labelField'=>'nickname','pickerSchema'=>AmisService::getSysUserList()]),
            shopwwiAmisFields(trans('field.created_at',[],'messages'),'created_at')->tableColumn(['width'=>145,'sortable'=>true])->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'created_at_betweenAmisTime'])->showOnUpdate(0)->showOnCreation(0),
            shopwwiAmisFields(trans('field.updated_user_id',[],'messages'),'updated_user_id')->showOnUpdate(0)->showOnCreation(0)->tableColumn(['width'=>60,'toggled'=>false,'sortable'=>true])->filterColumn('picker',['joinValues'=>true,'source'=>shopwwiAdminUrl('system/user/list?_format=json'),'size'=>'lg','valueField'=>'id','labelField'=>'nickname','pickerSchema'=>AmisService::getSysUserList()]),
            shopwwiAmisFields(trans('field.updated_at',[],'messages'),'updated_at')->tableColumn(['width'=>145,'toggled'=>false,'sortable'=>true])->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'updated_at_betweenAmisTime'])->showOnUpdate(0)->showOnCreation(0),
            shopwwiAmisFields(trans('field.deleted_at',[],'messages'),'deleted_at')->tableColumn(['width'=>145,'sortable'=>true])->showOnUpdate(0)->showOnCreation(0)->showOnIndex(4)->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'deleted_at_betweenAmisTime']),
        ];
    }
    /**
     * 添加关联关系
     * @param $model
     * @return mixed
     */
    protected function beforeJsonList($model){
        $model = $model->select($this->filterJsonFiled())->with(['class'=>function($q){
            $q->select('id','name');
        }]);
        return $model;
    }

    /**
     * 新增查询赋值
     * @return array
     */
    protected function getCreate(){
        return ['classList'=>SysHelpClass::get(),'sort'=>999];
    }

    /**
     * 编辑查询赋值
     * @param $info
     * @param $id
     * @return array
     */
    protected function insertGetEdit($info,$id){
        $data['info'] = $info;
        $data['classList'] = SysHelpClass::get();
        return $data;
    }

}
