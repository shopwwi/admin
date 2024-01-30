<?php
/**
 *-------------------------------------------------------------------------s*
 * 消息模板库控制器
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


class SysMsgTplCommonController extends AdminController
{
    public $model = \Shopwwi\Admin\App\Admin\Models\SysMsgTplCommon::class;
    public  $orderBy = [];
    protected $activeKey = 'settingSiteMsgCommon';
    protected $trans = 'sysMsgTplCommon'; // 语言文件名称
    protected $queryPath = 'system/msg/common'; // 完整路由地址
    protected $key = 'code';
    protected $useCreateDialog = 2;
    protected $useEditDialog = 2;
    /**
     * 路由注册
     * @var string
     */
     public $routePath = 'msg/common'; // 当前路由模块不填写则直接控制器名
    // public $routeAction = ['index'=>['GET','POST']]; //方法注册 未填写的则直接any
    // public $noNeedLogin = []; //不需要登入
    // public $noNeedAuth = []; //需要登入不需要鉴权
    /**
     * 数据字段处理
     * @return array
     */
    protected function fields(){
        $openOrClose = DictTypeService::getAmisDictType('openOrClose');
        return [
            shopwwiAmisFields(trans('field.code',[],'sysMsgTplCommon'),'code')->rules('required')->column('input-text',['md'=>12])->updateColumn('input-text',['md'=>12,'disabled'=>true])->showOnUpdate(3),
            shopwwiAmisFields(trans('field.name',[],'sysMsgTplCommon'),'name')->rules('required')->column('input-text',['md'=>12]),
            shopwwiAmisFields(trans('field.type',[],'sysMsgTplCommon'),'type')->rules('required')->column('input-text',['md'=>12]),
            shopwwiAmisFields(trans('field.class',[],'sysMsgTplCommon'),'class')->rules('required')->column('input-text',['md'=>12]),
            shopwwiAmisFields(trans('field.email_status',[],'sysMsgTplCommon'),'email_status')->rules(['bail','required','in:1,0'])->tableColumn(['sortable'=>true,'type'=>'mapping','map'=>$this->toMappingSelect($openOrClose,'${email_status}')])->filterColumn('select',['options'=>$openOrClose])->column('switch',['trueValue'=>1,'falseValue'=>0,'md'=>12]),
            shopwwiAmisFields(trans('field.email_title',[],'sysMsgTplCommon'),'email_title')->rules('required')->column('input-text',['md'=>12])->showOnIndex(2),
            shopwwiAmisFields(trans('field.email_content',[],'sysMsgTplCommon'),'email_content')->rules('required')->showOnIndex(2)->column('input-rich-text',['md'=>12])->showColumn('control',['body'=>['type'=>'tpl','tpl'=>'${email_content|raw}','md'=>12]]),
            shopwwiAmisFields(trans('field.notice_content',[],'sysMsgTplCommon'),'notice_content')->rules('required')->column('textarea',['md'=>12])->showOnIndex(2),
            shopwwiAmisFields(trans('field.sms_status',[],'sysMsgTplCommon'),'sms_status')->rules(['bail','required','in:1,0'])->tableColumn(['sortable'=>true,'type'=>'mapping','map'=>$this->toMappingSelect($openOrClose,'${sms_status}')])->filterColumn('select',['options'=>$openOrClose])->column('switch',['trueValue'=>1,'falseValue'=>0,'md'=>12]),
            shopwwiAmisFields(trans('field.sms_content',[],'sysMsgTplCommon'),'sms_content')->rules('required')->column('textarea',['md'=>12])->showOnIndex(2),
            shopwwiAmisFields(trans('field.wechat_status',[],'sysMsgTplCommon'),'wechat_status')->rules(['bail','required','in:1,0'])->tableColumn(['sortable'=>true,'type'=>'mapping','map'=>$this->toMappingSelect($openOrClose,'${wechat_status}')])->filterColumn('select',['options'=>$openOrClose])->column('switch',['trueValue'=>1,'falseValue'=>0,'md'=>12]),
            shopwwiAmisFields(trans('field.wechat_data_params',[],'sysMsgTplCommon'),'wechat_data_params')->showOnIndex(2)->column('json-editor',['md'=>12]),
            shopwwiAmisFields(trans('field.wechat_mp_template_id',[],'sysMsgTplCommon'),'wechat_mp_template_id')->showOnIndex(2)->column('input-text',['md'=>12]),
            shopwwiAmisFields(trans('field.wechat_mp_template_store_id',[],'sysMsgTplCommon'),'wechat_mp_template_store_id')->showOnIndex(2)->column('input-text',['md'=>12]),
            shopwwiAmisFields(trans('field.wechat_mp_template_store_title',[],'sysMsgTplCommon'),'wechat_mp_template_store_title')->showOnIndex(2)->column('input-text',['md'=>12]),
            shopwwiAmisFields(trans('field.wechat_template_url',[],'sysMsgTplCommon'),'wechat_template_url')->showOnIndex(2)->column('input-text',['md'=>12]),
            shopwwiAmisFields(trans('field.created_user_id',[],'messages'),'created_user_id')->showOnUpdate(0)->showOnCreation(0)->tableColumn(['width'=>60,'toggled'=>false,'sortable'=>true])->filterColumn('picker',['joinValues'=>true,'source'=>shopwwiAdminUrl('system/user/list?_format=json'),'size'=>'lg','valueField'=>'id','labelField'=>'nickname','pickerSchema'=>AmisService::getSysUserList()]),
            shopwwiAmisFields(trans('field.created_at',[],'messages'),'created_at')->tableColumn(['width'=>145,'sortable'=>true])->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'created_at_betweenAmisTime'])->showOnUpdate(0)->showOnCreation(0),
            shopwwiAmisFields(trans('field.updated_user_id',[],'messages'),'updated_user_id')->showOnUpdate(0)->showOnCreation(0)->tableColumn(['width'=>60,'toggled'=>false,'sortable'=>true])->filterColumn('picker',['joinValues'=>true,'source'=>shopwwiAdminUrl('system/user/list?_format=json'),'size'=>'lg','valueField'=>'id','labelField'=>'nickname','pickerSchema'=>AmisService::getSysUserList()]),
            shopwwiAmisFields(trans('field.updated_at',[],'messages'),'updated_at')->tableColumn(['width'=>145,'toggled'=>false,'sortable'=>true])->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'updated_at_betweenAmisTime'])->showOnUpdate(0)->showOnCreation(0),
            shopwwiAmisFields(trans('field.deleted_at',[],'messages'),'deleted_at')->tableColumn(['width'=>145,'sortable'=>true])->showOnUpdate(0)->showOnCreation(0)->showOnIndex(4)->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'deleted_at_betweenAmisTime']),
        ];
    }

    protected function getCreate()
    {
        return [ 'email_status'=>0,'sms_status'=>0,'wechat_status'=>0];
    }

}
