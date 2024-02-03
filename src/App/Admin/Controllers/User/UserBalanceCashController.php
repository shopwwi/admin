<?php
/**
 *-------------------------------------------------------------------------s*
 * 会员余额提现控制器
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

use Shopwwi\Admin\App\Admin\Service\AdminService;
use Shopwwi\Admin\App\Admin\Service\AmisService;
use Shopwwi\Admin\App\Admin\Service\DictTypeService;
use Shopwwi\Admin\App\Admin\Service\SysConfigService;
use Shopwwi\Admin\App\Admin\Service\User\BalanceService;
use Shopwwi\Admin\Libraries\Amis\AdminController;
use Shopwwi\WebmanAuth\Facade\Auth;
use support\Request;
use support\Response;

class UserBalanceCashController extends AdminController
{
    protected $model = \Shopwwi\Admin\App\User\Models\UserBalanceCash::class;
    protected $orderBy = ['id' => 'desc'];
    protected $trans = 'userBalanceCash'; // 语言文件名称
    protected $queryPath = 'user/cash'; // 完整路由地址
    protected $useHasCreate = false;
    protected $activeKey = 'userBalanceCashIndex';
    /**
     * 路由注册
     * @var string
     */
     public $routePath = 'cash'; // 当前路由模块不填写则直接控制器名
    // public $routeAction = ['index'=>['GET','POST']]; //方法注册 未填写的则直接any
     public $routeNoAction = ['create','store','destroy','recovery','restore','erasure']; //不允许方法注册
    // public $noNeedLogin = []; //不需要登入
    // public $noNeedAuth = []; //需要登入不需要鉴权
    /**
     * 数据字段处理
     * @return array
     */
    protected function fields(){
        $userCardType = DictTypeService::getAmisDictType('userCardType');
        $cashStatus = DictTypeService::getAmisDictType('cashStatus');
        return [
            shopwwiAmisFields(trans('field.id',[],'messages'),'id')->tableColumn(['width'=>60,'sortable'=>true])->showOnUpdate(0)->showOnCreation(0)->showFilter(),
            shopwwiAmisFields(trans('field.cash_sn',[],'userBalanceCash'),'cash_sn')->column('input-text',['static'=>true]),
            shopwwiAmisFields(trans('field.user_id',[],'userBalanceCash'),'user_id')->column('input-text',['static'=>true])->tableColumn(['sortable'=>true,'type'=>'tpl','tpl'=>'<span class=\'cxd-Tag\'>${user.nickname}(ID:${user_id})</span>'])
                ->filterColumn('picker',['joinValues'=>true,'source'=>shopwwiAdminUrl('users/list?_format=json'),'size'=>'lg','valueField'=>'id','labelField'=>'nickname','pickerSchema'=>AmisService::getUserList()]),
            shopwwiAmisFields(trans('field.cash_amount',[],'userBalanceCash'),'cash_amount')->tableColumn(['classNameExpr'=>'text-yellow-600']),
            shopwwiAmisFields(trans('field.service_amount',[],'userBalanceCash'),'service_amount')->rules('required'),
            shopwwiAmisFields(trans('field.amount',[],'userBalanceCash'),'amount')->tableColumn(['classNameExpr'=>'text-success']),
            shopwwiAmisFields(trans('field.pay_time',[],'userBalanceCash'),'pay_time')->column('input-datetime',['format'=>'YYYY-MM-DD HH:mm:ss']),
            shopwwiAmisFields(trans('field.bank_name',[],'userBalanceCash'),'bank_name'),
            shopwwiAmisFields(trans('field.bank_account',[],'userBalanceCash'),'bank_account')->showOnIndex(2),
            shopwwiAmisFields(trans('field.bank_username',[],'userBalanceCash'),'bank_username'),
            shopwwiAmisFields(trans('field.bank_branch',[],'userBalanceCash'),'bank_branch')->showOnIndex(2),
            shopwwiAmisFields(trans('field.bank_type',[],'userBalanceCash'),'bank_type')->filterColumn('select',['options'=>$userCardType])
                ->tableColumn(['sortable'=>true,'align'=>'center','type'=>'mapping','map'=>$this->toMappingSelect($userCardType,'${bank_type}','default')])
                ->column('select',['selectFirst'=>true,'options'=>$userCardType]),
            shopwwiAmisFields(trans('field.out_sn',[],'userBalanceCash'),'out_sn')->showOnIndex(2),
            shopwwiAmisFields(trans('field.cash_status',[],'userBalanceCash'),'cash_status')->filterColumn('select',['options'=>$cashStatus])
                ->tableColumn(['sortable'=>true,'align'=>'center','type'=>'mapping','map'=>$this->toMappingSelect($cashStatus,'${cash_status}','default')])
                ->column('select',['selectFirst'=>true,'options'=>$cashStatus]),
            shopwwiAmisFields(trans('field.refuse_reason',[],'userBalanceCash'),'refuse_reason')->column('textarea',['visibleOn'=>'this.cash_status == 2','md'=>12])->showOnIndex(2),
            shopwwiAmisFields(trans('field.sys_user_id',[],'userBalanceCash'),'sys_user_id')->showOnUpdate(0)->tableColumn(['toggled'=>false]),
            shopwwiAmisFields(trans('field.sys_user_name',[],'userBalanceCash'),'sys_user_name')->showOnUpdate(0)->tableColumn(['toggled'=>false]),
            shopwwiAmisFields(trans('field.created_at',[],'messages'),'created_at')->tableColumn(['width'=>145,'sortable'=>true])->filterColumn('input-datetime-range',['format'=> 'YYYY-MM-DD HH:mm:ss','name'=>'created_at_betweenAmisTime'])->showOnUpdate(0)->showOnCreation(0),
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
        $model = $model->select($this->filterJsonFiled());
        $model->with(['user'=>function($q){
            $q->select('id','username','avatar','nickname');
        }]);
        return $model;
    }
    /**
     * 编辑返回数据插入
     * @param $info
     * @param $id
     * @return mixed
     */
    protected function insertGetEdit($info,$id){
        $info->load(['user'=>function($q){
            $q->select('id','username','avatar','nickname');
        }]);
        $data['info'] = $info;
        return $data;
    }

    /**
     * 审核
     * @param Request $request
     * @param $id
     * @return Response
     * @throws \Throwable
     */
    public function update(Request $request,$id)
    {
        $user = Auth::guard($this->guard)->fail()->user();
        $params = shopwwiParams(['cash_status'=>'PASS','refuse_reason', 'pay_time', 'out_sn']);
        try {
            $info = BalanceService::adminTrimCash($params,$id,$user->id,$user->username);
            AdminService::addLog('E','1',$this->projectName.trans('update',[],'messages').$this->name."(".trans('number',[],'messages')."：{$id})",$user->id,$user->username,$params);
            return shopwwiSuccess($info,trans('update',[],'messages').$this->name.trans('success',[],'messages'));
        } catch (\Exception $e) {
            AdminService::addLog('E','0',$this->projectName.trans('update',[],'messages').$this->name."(".trans('number',[],'messages')."：{$id})",$user->id,$user->username,$params,$e->getMessage());
            return shopwwiError( $e->getMessage());
        }
    }

    /**
     * 提现设置
     * @param Request $request
     * @return Response
     */
    public function setting(Request $request)
    {
        try {
            if($request->method() === 'GET'){
                if($this->format() == 'json'){
                    $info = SysConfigService::getFirstOrCreate([
                        'key' => 'cash'
                    ],['name'=>'提现配置','value'=>[
                        'used' => '1', // 开启提现
                        'isAutoAudit' => '0',// 是否需要审核 0 手动审核  1 自动审核
                        'isAutoTransfer' => '0',// 是否自动转账 0 手动转账  1 自动转账
                        'rate' => '0', // 提现手续费比率 (0-100)
                        'min' => '0', // 单次最低提现
                        'max' => '100000', // 单次最高提现
                        'rule' => [
                            'time' => '3', // 周期时长（天）
                            'num' => '3' //周期可提现次数
                        ]
                    ]]);
                    return shopwwiSuccess($info);
                }
                $openOrClose = DictTypeService::getAmisDictType('openOrClose');
                $form = $this->baseForm()->body([
                    shopwwiAmis('alert')->body('设置周期规则，则表示周期内X天最多可提现Y次'),
                    shopwwiAmis('grid')->gap('lg')->columns([
                        shopwwiAmis('radios')->name('cash.used')->label(trans('config.used',[],$this->trans))->selectFirst(true)->options($openOrClose)->xs(12),
                        shopwwiAmis('radios')->name('cash.isAutoAudit')->label(trans('config.isAutoAudit',[],$this->trans))->selectFirst(true)->options([['label'=>'手动审核','value'=>'0'],['label'=>'自动审核','value'=>'1']])->xs(12),
                        shopwwiAmis('radios')->name('cash.isAutoTransfer')->label(trans('config.isAutoTransfer',[],$this->trans))->selectFirst(true)->options([['label'=>'手动转账','value'=>'0'],['label'=>'自动转账','value'=>'1']])->xs(12),
                        shopwwiAmis('input-number')->name('cash.rate')->precision(2)->label(trans('config.rate',[],$this->trans))->min(0)->max(100)->xs(12)->description('提现手续费比率 (0-100)'),
                        shopwwiAmis('input-number')->name('cash.min')->precision(2)->label(trans('config.min',[],$this->trans))->min(0.01)->xs(12)->description('单次最低可提现金额'),
                        shopwwiAmis('input-number')->name('cash.max')->precision(2)->label(trans('config.max',[],$this->trans))->min(0)->xs(12)->description('单次最高可提现金额'),
                        shopwwiAmis('combo')->name('cash.rule')->label(trans('config.rule',[],$this->trans))->items([
                            shopwwiAmis('input-text')->name('time')->label('每期')->suffix('天'),
                            shopwwiAmis('input-text')->name('num')->label('次数'),
                        ]),
                    ])
                ])->api('post:' . shopwwiAdminUrl('user/cash/setting'))
                    ->actions([
                        shopwwiAmis('reset')->label(trans('reset',[],'messages')),
                        shopwwiAmis('submit')->label(trans('submit',[],'messages'))->level('primary')
                    ])
                    ->initApi(shopwwiAdminUrl('user/cash/setting?_format=json'));
                $page = $this->basePage()->body($form);
                $page = $page->subTitle(trans('config.title',[],$this->trans));
                if($this->format() == 'data' || $this->format() == 'web') return shopwwiSuccess($page);
                return $this->getAdminView(['json'=>$page,'activeKey'=>'userBalanceCashSite']);
            }else{
                $params = shopwwiParams(['cash']);
                SysConfigService::updateSetting($params);
            }
            return shopwwiSuccess();
        }catch (\Exception $e){
            return shopwwiError($e->getMessage());
        }
    }

}
