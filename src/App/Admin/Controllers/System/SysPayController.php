<?php
/**
 *-------------------------------------------------------------------------s*
 * 外部支付流水控制器
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

use Shopwwi\Admin\Amis\DropdownButton;
use Shopwwi\Admin\Amis\Operation;
use Shopwwi\Admin\App\Admin\Models\SysPayment;
use Shopwwi\Admin\App\Admin\Service\AmisService;
use Shopwwi\Admin\App\Admin\Service\DictTypeService;
use Shopwwi\Admin\Libraries\Amis\AdminController;
use Shopwwi\Admin\Logic\PayLogic;

class SysPayController extends AdminController
{
    protected $model = \Shopwwi\Admin\App\Admin\Models\SysPay::class;
    protected $orderBy = ['id' => 'desc'];
    protected $trans = 'sysPay'; // 语言文件名称
    protected $queryPath = 'system/pay'; // 完整路由地址
    protected $activeKey = 'settingWayPayIndex';
    protected $useHasCreate = false;
    protected $useHasDestroy = false;
    /**
     * 路由注册
     * @var string
     */
     public $routePath = 'pay'; // 当前路由模块不填写则直接控制器名
//     public $routeAction = ['index'=>['GET','POST']]; //方法注册 未填写的则直接any
     public $routeNoAction = ['create','store','destroy','recovery','restore','erasure']; //不允许方法注册
//     public $noNeedLogin = []; //不需要登入
//     public $noNeedAuth = []; //需要登入不需要鉴权

    /**
     * 操作列
     * @return Operation
     */
    protected function rowActions(): Operation
    {
        return Operation::make()->label(trans('actions',[],'messages'))->fixed('right')->width(160)->buttons([
            $this->rowEditButton($this->useEditDialog,'$'.$this->key),
            $this->rowShowButton($this->useShowDialog,'$'.$this->key)
        ]);
    }

    /**
     * 数据字段处理
     * @return array
     */
    protected function fields(){
        $payStatus = DictTypeService::getAmisDictType('payStatus');
        $payType = DictTypeService::getAmisDictType('payType');
        return [
            shopwwiAmisFields(trans('field.id',[],'messages'),'id')->tableColumn(['width'=>60,'sortable'=>true])->showOnUpdate(0)->showOnCreation(0)->showFilter(),
            shopwwiAmisFields(trans('field.pay_sn',[],'sysPay'),'pay_sn')->showOnUpdate(4)->showOnCreation(2),
            shopwwiAmisFields(trans('field.amount',[],'sysPay'),'amount')->rules(['required','numeric','min:0'])->column('input-number',['min'=>0,'precision'=>2]),
            shopwwiAmisFields(trans('field.refund',[],'sysPay'),'refund')->rules('required')->column('input-number',['min'=>0,'precision'=>2]),
            shopwwiAmisFields(trans('field.payment_name',[],'sysPay'),'payment_name')->showOnUpdate(0),
            shopwwiAmisFields(trans('field.payment_code',[],'sysPay'),'payment_code')->rules('required')->column('select',['source'=>'$paymentList','labelField'=>'name','valueField'=>'code']),
            shopwwiAmisFields(trans('field.status',[],'sysPay'),'status')->rules(['bail','required','in:1,0'])
                ->tableColumn(['sortable'=>true,'type'=>'mapping','map'=>$this->toMappingSelect($payStatus,'${status}','text')])
                ->column('select',['options'=>$payStatus])
                ->filterColumn('select',['options'=>$payStatus]),
            shopwwiAmisFields(trans('field.pay_time',[],'sysPay'),'pay_time')->column('input-datetime',['format'=> 'YYYY-MM-DD HH:mm:ss']),
            shopwwiAmisFields(trans('field.pay_type',[],'sysPay'),'pay_type')
                ->tableColumn(['sortable'=>true,'type'=>'mapping','map'=>$this->toMappingSelect($payType,'${pay_type}','text')])
                ->column('select',['options'=>$payType])
                ->filterColumn('select',['options'=>$payType]),
            shopwwiAmisFields(trans('field.pay_client_type',[],'sysPay'),'pay_client_type')->rules('required'),
            shopwwiAmisFields(trans('field.pay_type_id',[],'sysPay'),'pay_type_id')->showOnUpdate(4),
            shopwwiAmisFields(trans('field.pay_return',[],'sysPay'),'pay_return'),
            shopwwiAmisFields(trans('field.out_sn',[],'sysPay'),'out_sn')->rules('required'),
            shopwwiAmisFields(trans('field.user_id',[],'sysPay'),'user_id')->rules('required'),
            shopwwiAmisFields(trans('field.reason',[],'sysPay'),'reason')->rules('required')->column('textarea',['md'=>12]),
            shopwwiAmisFields(trans('field.updated_user_id',[],'messages'),'updated_user_id')->showOnCreation(false)->showOnUpdate(false)->filterColumn('picker',['joinValues'=>true,'source'=>shopwwiAdminUrl('system/user/list?_format=json'),'size'=>'lg','valueField'=>'id','labelField'=>'nickname','pickerSchema'=>AmisService::getSysUserList()]),
            shopwwiAmisFields(trans('field.created_at',[],'messages'),'created_at')->tableColumn(['width'=>145,'sortable'=>true])->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'created_at_betweenAmisTime'])->showOnUpdate(0)->showOnCreation(0),
            shopwwiAmisFields(trans('field.updated_at',[],'messages'),'updated_at')->tableColumn(['width'=>145,'toggled'=>false,'sortable'=>true])->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'updated_at_betweenAmisTime'])->showOnUpdate(0)->showOnCreation(0),
        ];
    }

    /**
     * 编辑返回数据插入
     * @param $info
     * @param $id
     * @return mixed
     */
    protected function insertGetEdit($info,$id){
        $data['paymentList'] = SysPayment::whereNotIn('code',['offline','balance'])->get();
        $data['info'] = $info;
        return $data;
    }

    /**
     * 修改中的拦截
     * @param $user
     * @param $params
     * @param $info
     * @param $oldInfo
     * @return void
     * @throws \Exception
     */
    protected function insertUpdating($user,$params,&$info,$oldInfo){
        if(!empty($oldInfo->status)){
            throw new \Exception('此状态下不允许编辑');
        }
        if(!empty($info->payment_code)){
            $sysPayment = SysPayment::where('code',$info->payment_code)->first();
            if($sysPayment != null){
                $info->payment_name = $sysPayment->name;
            }
        }
        if(!empty($info->status)){ //变更支付状态处理
            PayLogic::adminPay($info);
        }
    }

}
