<?php
/**
 *-------------------------------------------------------------------------s*
 * 用户等级组控制器
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
namespace Shopwwi\Admin\App\Admin\Controllers\User;

use Shopwwi\Admin\App\Admin\Service\AmisService;
use Shopwwi\Admin\App\Admin\Service\DictTypeService;
use Shopwwi\Admin\Libraries\Amis\AdminController;


class UserGradeGroupController extends AdminController
{
    protected $model = \Shopwwi\Admin\App\User\Models\UserGradeGroup::class;
    protected $orderBy = ['id' => 'desc'];
    protected $adminOp = true;
    protected $trans = 'userGradeGroup'; // 语言文件名称
    protected $queryPath = 'user/grade/group'; // 完整路由地址
    protected $activeKey = 'userRightsGroup';
    protected $useHasRecovery = true;

    /**
     * 路由注册
     * @var string
     */
     public $routePath = 'grade/group'; // 当前路由模块不填写则直接控制器名
    // public $routeAction = ['index'=>['GET','POST']]; //方法注册 未填写的则直接any
    // public $routeNoAction = ['index']; //不允许方法注册
    // public $noNeedLogin = []; //不需要登入
    // public $noNeedAuth = []; //需要登入不需要鉴权
    /**
     * 数据字段处理
     * @return array
     */
    protected function fields(){
        $gradeGroupType = DictTypeService::getAmisDictType('gradeGroupType');
        $yesOrNo = DictTypeService::getAmisDictType('yesOrNo');
        return [
            shopwwiAmisFields(trans('field.id',[],'messages'),'id')->tableColumn(['width'=>60,'sortable'=>true])->showOnUpdate(0)->showOnCreation(0)->showFilter(),
            shopwwiAmisFields(trans('field.name',[],'userGradeGroup'),'name')->rules(['bail','required','min:2','max:20'])->tableColumn(['sortable'=>true,'copyable'=>true,'type'=>'link','href'=>$this->getUrl('user/grades?group_id=${id}'),'body'=>'${name}','blank'=>false]),
            shopwwiAmisFields(trans('field.type',[],'userGradeGroup'),'type')->tableColumn(['sortable'=>true,'type'=>'mapping','map'=>$this->toMappingSelect($gradeGroupType,'${type}','default')])
                ->filterColumn('select',['options'=>$gradeGroupType])
                ->column('select',['options'=>$gradeGroupType])->rules(['bail','required','in:0,1,2'])->showOnUpdate(4),
            shopwwiAmisFields(trans('field.rule',[],'userGradeGroup'),'rule')->showOnIndex(2)->column('json-schema',['schema'=> ['type'=>'object','additionalProperties'=>false,
                'placeholder' => ['title'=>'请输入价格'],
                'properties'=>['day'=>['type'=>'number','title'=>'一天'],'week'=>['type'=>'number','title'=>'一周'],'month'=>['type'=>'number','title'=>'一月'],'year'=>['type'=>'number','title'=>'一年']]],'md'=>12,'description'=>'购买周期 --- 购买金额 ','requiredOn'=>'this.type > 0','hiddenOn'=>'this.type < 1||!this.type','clearValueOnHidden'=>true]),
            shopwwiAmisFields(trans('field.is_default',[],'userGradeGroup'),'is_default')->tableColumn(['sortable'=>true,'type'=>'mapping','map'=>$this->toMappingSelect($yesOrNo,'${is_default}','default')])
                ->filterColumn('select',['options'=>$yesOrNo])->column('radios',['options'=>$yesOrNo])
                ->showOnCreation(0)->rules(['bail','nullable','in:1,0']),
            shopwwiAmisFields(trans('field.created_user_id',[],'messages'),'created_user_id')->showOnUpdate(0)->showOnCreation(0)->tableColumn(['width'=>60,'toggled'=>false,'sortable'=>true])->filterColumn('picker',['joinValues'=>true,'source'=>shopwwiAdminUrl('system/user/list?_format=json'),'size'=>'lg','valueField'=>'id','labelField'=>'nickname','pickerSchema'=>AmisService::getSysUserList()]),
            shopwwiAmisFields(trans('field.created_at',[],'messages'),'created_at')->tableColumn(['width'=>145,'sortable'=>true])->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'created_at_betweenAmisTime'])->showOnUpdate(0)->showOnCreation(0),
            shopwwiAmisFields(trans('field.updated_user_id',[],'messages'),'updated_user_id')->showOnUpdate(0)->showOnCreation(0)->tableColumn(['width'=>60,'toggled'=>false,'sortable'=>true])->filterColumn('picker',['joinValues'=>true,'source'=>shopwwiAdminUrl('system/user/list?_format=json'),'size'=>'lg','valueField'=>'id','labelField'=>'nickname','pickerSchema'=>AmisService::getSysUserList()]),
            shopwwiAmisFields(trans('field.updated_at',[],'messages'),'updated_at')->tableColumn(['width'=>145,'toggled'=>false,'sortable'=>true])->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'updated_at_betweenAmisTime'])->showOnUpdate(0)->showOnCreation(0),
            shopwwiAmisFields(trans('field.deleted_at',[],'messages'),'deleted_at')->tableColumn(['width'=>145,'sortable'=>true])->showOnUpdate(0)->showOnCreation(0)->showOnIndex(4)->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'deleted_at_betweenAmisTime']),
        ];
    }

}
