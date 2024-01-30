<?php
/**
 *-------------------------------------------------------------------------s*
 * 系统导航控制器
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


class SysNavigationController extends AdminController
{
    protected $model = \Shopwwi\Admin\App\Admin\Models\SysNavigation::class;
    protected $orderBy = ['id' => 'desc'];
    protected $activeKey = 'settingSiteNavigation';
    protected $trans = 'sysNavigation'; // 语言文件名称
    protected $queryPath = 'system/navigation'; // 完整路由地址
    protected $adminOp = true;
    /**
     * 路由注册
     * @var string
     */
     public $routePath = 'navigation'; // 当前路由模块不填写则直接控制器名
    // public $routeAction = ['index'=>['GET','POST']]; //方法注册 未填写的则直接any
     public $routeNoAction = ['recovery']; //不允许方法注册
    // public $noNeedLogin = []; //不需要登入
    // public $noNeedAuth = []; //需要登入不需要鉴权
    /**
     * 数据字段处理
     * @return array
     */
    protected function fields(){
        $yesOrNo = DictTypeService::getAmisDictType('yesOrNo');
        $sysNavigationPosition = DictTypeService::getAmisDictType('sysNavigationPosition');
        return [
            shopwwiAmisFields(trans('field.id',[],'messages'),'id')->tableColumn(['width'=>60,'sortable'=>true])->showOnUpdate(0)->showOnCreation(0)->showFilter(),
            shopwwiAmisFields(trans('field.name',[],'sysNavigation'),'name')->rules(['bail','required','min:1','max:30']),
            shopwwiAmisFields(trans('field.link',[],'sysNavigation'),'link')->column('input-text',['description'=>'请填写带http或者https的链接，不携带http或https将会根据应用场景自动生成场景链接']),
            shopwwiAmisFields(trans('field.icon',[],'sysNavigation'),'icon'),
            shopwwiAmisFields(trans('field.sort',[],'messages'),'sort')->tableColumn(['width'=>60,'sortable'=>true])->rules(['bail','required','numeric','min:0','max:999'])->column('input-number',['min'=>0,'max'=>999]),
            shopwwiAmisFields(trans('field.image',[],'sysNavigation'),'image')->column('hidden',['md'=>12])->showOnIndex(2),
            shopwwiAmisFields(trans('field.image',[],'sysNavigation'),'imageUrl')->column('input-image',['md'=>12,'autoFill'=>['image'=>'${file_name}'],'crop'=>['aspectRatio'=>3],'receiver'=>shopwwiAdminUrl('common/upload')])->showColumn('control',['body'=>['type'=>'image','name'=>'imageUrl','width'=>90,'height'=>90]])->showOnIndex(0)->showOnCreation(3)->showOnUpdate(3),
            shopwwiAmisFields(trans('field.status',[],'sysNavigation'),'status')->rules(['bail','required','in:1,0'])->tableColumn(['sortable'=>true,'type'=>'mapping','map'=>$this->toMappingSelect($yesOrNo,'${status}')])->filterColumn('select',['options'=>$yesOrNo])->column('switch',['trueValue'=>1,'falseValue'=>0]),
            shopwwiAmisFields(trans('field.is_blank',[],'sysNavigation'),'is_blank')->rules(['bail','required','in:1,0'])->tableColumn(['sortable'=>true,'type'=>'mapping','map'=>$this->toMappingSelect($yesOrNo,'${is_blank}')])->filterColumn('select',['options'=>$yesOrNo])->column('switch',['trueValue'=>1,'falseValue'=>0]),
            shopwwiAmisFields(trans('field.position',[],'sysNavigation'),'position')->rules(['bail','required','in:0,1,2'])->tableColumn(['sortable'=>true,'type'=>'mapping','map'=>$this->toMappingSelect($sysNavigationPosition,'${position}','default')])->filterColumn('select',['options'=>$sysNavigationPosition])->column('radios',['md'=>12,'selectFirst'=>true,'options'=>$sysNavigationPosition]),
            shopwwiAmisFields(trans('field.code',[],'sysNavigation'),'code')->column('input-text',['description'=>'常用于判断导航高亮所用，可不填写']),
            shopwwiAmisFields(trans('field.app',[],'sysNavigation'),'app')->column('input-text',['description'=>'用于对导航进行类别筛选，不同模块对应字符不同'])->showFilter(),
            shopwwiAmisFields(trans('field.created_user_id',[],'messages'),'created_user_id')->showOnUpdate(0)->showOnCreation(0)->tableColumn(['width'=>60,'toggled'=>false,'sortable'=>true])->showFilter(),
            shopwwiAmisFields(trans('field.created_at',[],'messages'),'created_at')->tableColumn(['width'=>145,'sortable'=>true])->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'created_at_betweenAmisTime'])->showOnUpdate(0)->showOnCreation(0),
            shopwwiAmisFields(trans('field.updated_user_id',[],'messages'),'updated_user_id')->showOnUpdate(0)->showOnCreation(0)->tableColumn(['width'=>60,'toggled'=>false,'sortable'=>true])->showFilter(),
            shopwwiAmisFields(trans('field.updated_at',[],'messages'),'updated_at')->tableColumn(['width'=>145,'toggled'=>false,'sortable'=>true])->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'updated_at_betweenAmisTime'])->showOnUpdate(0)->showOnCreation(0),
        ];
    }

    protected function getCreate()
    {
        return ['sort' => 999,'status'=>1,'is_blank' => 0];
    }

}
