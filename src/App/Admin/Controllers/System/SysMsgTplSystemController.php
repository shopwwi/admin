<?php
/**
 *-------------------------------------------------------------------------*
 * 系统模板控制器
 *-------------------------------------------------------------------------*
 * @author      8988354@qq.com TycoonSong
 *-------------------------------------------------------------------------*
 */
namespace Shopwwi\Admin\App\Admin\Controllers\System;

use Shopwwi\Admin\App\Admin\Service\AmisService;
use Shopwwi\Admin\App\Admin\Service\DictTypeService;
use Shopwwi\Admin\Libraries\Amis\AdminController;

class SysMsgTplSystemController extends AdminController
{
    protected $model = \Shopwwi\Admin\App\Admin\Models\SysMsgTplSystem::class;
    protected $orderBy = [];

    protected $trans = 'sysMsgTplSystem'; // 语言文件名称
    protected $queryPath = 'system/msg/system'; // 完整路由地址
    protected $useHasRecovery = true;
    protected $activeKey = 'settingSiteMsgSystem';
    protected $key = 'code';

    /**
     * 路由注册
     * @var string
     */
     public $routePath = 'msg/system'; // 当前路由模块不填写则直接控制器名
    // public $routeAction = ['index'=>['GET','POST']]; //方法注册 未填写的则直接any
    // public $routeNoAction = ['index']; //不允许方法注册
    // public $noNeedLogin = []; //不需要登入
    // public $noNeedAuth = []; //需要登入不需要鉴权
    /**
     * 数据字段处理
     * @return array
     */
    protected function fields(){
        $msgTplSendType = DictTypeService::getAmisDictType('msgTplSendType');
        return [
            shopwwiAmisFields(trans('field.code',[],'sysMsgTplSystem'),'code')->rules('required')->column('input-text',['md'=>12])->updateColumn('input-text',['md'=>12,'disabled'=>true])->showOnUpdate(3),
            shopwwiAmisFields(trans('field.name',[],'sysMsgTplSystem'),'name')->tableColumn(['type'=>'tpl','tpl'=>'${name| raw}'])->rules('required')->column('input-text',['md'=>12]),
            shopwwiAmisFields(trans('field.send_type',[],'sysMsgTplSystem'),'send_type')->rules('required')->tableColumn(['sortable'=>true,'type'=>'mapping','map'=>$this->toMappingSelect($msgTplSendType,'${send_type}')])->filterColumn('select',['options'=>$msgTplSendType])
                ->column('radios',['options'=>$msgTplSendType,'md'=>12,'selectFirst'=>true])->updateColumn('radios',['options'=>$msgTplSendType,'md'=>12,'selectFirst'=>true,'disabled'=>true])->showOnUpdate(3),
            shopwwiAmisFields(trans('field.title',[],'sysMsgTplSystem'),'title')->column('input-text',['md'=>12]),
            shopwwiAmisFields(trans('field.content',[],'sysMsgTplSystem'),'content')->rules('required')->column('textarea',['md'=>12,'hiddenOn'=>"this.send_type == 'EMAIL'"])->showOnDetail(0)->showOnIndex(0),
            shopwwiAmisFields(trans('field.content',[],'sysMsgTplSystem'),'content')->rules('required')->column('input-rich-text',['md'=>12,'hiddenOn'=>"this.send_type == 'MSG'"])->showColumn('control',['body'=>['type'=>'tpl','tpl'=>'${content|raw}','md'=>12]])->showOnIndex(2),
            shopwwiAmisFields(trans('field.created_user_id',[],'messages'),'created_user_id')->showOnUpdate(0)->showOnCreation(0)->tableColumn(['width'=>60,'toggled'=>false,'sortable'=>true])->filterColumn('picker',['joinValues'=>true,'source'=>shopwwiAdminUrl('system/user/list?_format=json'),'size'=>'lg','valueField'=>'id','labelField'=>'nickname','pickerSchema'=>AmisService::getSysUserList()]),
            shopwwiAmisFields(trans('field.created_at',[],'messages'),'created_at')->tableColumn(['width'=>145,'sortable'=>true])->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'created_at_betweenAmisTime'])->showOnUpdate(0)->showOnCreation(0),
            shopwwiAmisFields(trans('field.updated_user_id',[],'messages'),'updated_user_id')->showOnUpdate(0)->showOnCreation(0)->tableColumn(['width'=>60,'toggled'=>false,'sortable'=>true])->filterColumn('picker',['joinValues'=>true,'source'=>shopwwiAdminUrl('system/user/list?_format=json'),'size'=>'lg','valueField'=>'id','labelField'=>'nickname','pickerSchema'=>AmisService::getSysUserList()]),
            shopwwiAmisFields(trans('field.updated_at',[],'messages'),'updated_at')->tableColumn(['width'=>145,'toggled'=>false,'sortable'=>true])->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'updated_at_betweenAmisTime'])->showOnUpdate(0)->showOnCreation(0),
            shopwwiAmisFields(trans('field.deleted_at',[],'messages'),'deleted_at')->tableColumn(['width'=>145,'sortable'=>true])->showOnUpdate(0)->showOnCreation(0)->showOnIndex(4)->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'deleted_at_betweenAmisTime'])
        ];
    }

}