<?php
/**
 *-------------------------------------------------------------------------s*
 * 支付方式控制器
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
use Shopwwi\Admin\Libraries\Validator;


class SysPaymentController extends AdminController
{
    protected $model = \Shopwwi\Admin\App\Admin\Models\SysPayment::class;
    protected $orderBy = ['id' => 'desc'];
    protected $trans = 'sysPayment'; // 语言文件名称
    protected $queryPath = 'system/payment'; // 完整路由地址
    protected $activeKey = 'settingWayPayPayment';
    /**
     * 路由注册
     * @var string
     */
     public $routePath = 'payment'; // 当前路由模块不填写则直接控制器名
    // public $routeAction = ['index'=>['GET','POST']]; //方法注册 未填写的则直接any
     public $routeNoAction = ['destroy','recovery']; //不允许方法注册
    // public $noNeedLogin = []; //不需要登入
    // public $noNeedAuth = []; //需要登入不需要鉴权
    /**
     * 数据字段处理
     * @return array
     */
    protected function fields(){
        $allowOrUnAllow = DictTypeService::getAmisDictType('allowOrUnAllow');
        $openOrClose = DictTypeService::getAmisDictType('openOrClose');
        return [
            shopwwiAmisFields(trans('field.id',[],'messages'),'id')->tableColumn(['width'=>60,'sortable'=>true])->showOnUpdate(0)->showOnCreation(0)->showFilter(),
            shopwwiAmisFields(trans('field.name',[],'sysPayment'),'name')->rules('required'),
            shopwwiAmisFields(trans('field.code',[],'sysPayment'),'code')->rules(['required','chs_unique:sys_payment']),
            shopwwiAmisFields(trans('field.config',[],'sysPayment'),'config')->column('json-editor',['md'=>12])->showOnIndex(0),
            shopwwiAmisFields(trans('field.wap',[],'sysPayment'),'wap')->filterColumn('select',['options'=>$allowOrUnAllow])
                ->tableColumn(['quickEdit' => shopwwiAmis('switch')->trueValue(1)->falseValue(0)->mode('inline')
                    ->saveImmediately(true)])->column('radios',['selectFirst'=>true,'options'=>$allowOrUnAllow])
                ->rules('required','in:1,0'),
            shopwwiAmisFields(trans('field.app',[],'sysPayment'),'app')->filterColumn('select',['options'=>$allowOrUnAllow])
                ->tableColumn(['quickEdit' => shopwwiAmis('switch')->trueValue(1)->falseValue(0)->mode('inline')
                    ->saveImmediately(true)])->column('radios',['selectFirst'=>true,'options'=>$allowOrUnAllow])
                ->rules('required','in:1,0'),
            shopwwiAmisFields(trans('field.web',[],'sysPayment'),'web')->filterColumn('select',['options'=>$allowOrUnAllow])
                ->tableColumn(['quickEdit' => shopwwiAmis('switch')->trueValue(1)->falseValue(0)->mode('inline')
                    ->saveImmediately(true)])->column('radios',['selectFirst'=>true,'options'=>$allowOrUnAllow])
                ->rules('required','in:1,0'),
            shopwwiAmisFields(trans('field.status',[],'sysPayment'),'status')->filterColumn('select',['options'=>$openOrClose])
                ->tableColumn(['quickEdit' => shopwwiAmis('switch')->trueValue(1)->falseValue(0)->mode('inline')
                ->saveImmediately(true)])->column('radios',['selectFirst'=>true,'options'=>$openOrClose])
                ->rules('required','in:1,0'),
            shopwwiAmisFields(trans('field.created_at',[],'messages'),'created_at')->tableColumn(['width'=>145,'sortable'=>true])->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'created_at_betweenAmisTime'])->showOnUpdate(0)->showOnCreation(0),
            shopwwiAmisFields(trans('field.updated_at',[],'messages'),'updated_at')->tableColumn(['width'=>145,'toggled'=>false,'sortable'=>true])->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'updated_at_betweenAmisTime'])->showOnUpdate(0)->showOnCreation(0),
        ];
    }

    protected function beforeStore($user,&$validator){
        $res =  $this->filterStore();
        $validator = Validator::make(\request()->all(), $res['rule'], [], $res['lang']);
        $params = shopwwiParams($res['filter']); //指定字段
        if(isset($params['config']) && is_string($params['config'])){
            $params['config'] = json_decode(trim($params['config']),true);
        }
        return $params;
    }

    protected function insertUpdating($user,$params,&$info,$oldInfo){
        if(isset($info->config) && is_string($info->config)){
            $info->config = json_decode(trim($info->config),true);
        }
    }
}
