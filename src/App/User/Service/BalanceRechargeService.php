<?php
/**
 *-------------------------------------------------------------------------s*
 *
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

namespace Shopwwi\Admin\App\User\Service;

use Shopwwi\Admin\Amis\Operation;
use Shopwwi\Admin\App\Admin\Service\DictTypeService;
use Shopwwi\Admin\App\Admin\Service\SysPayService;
use Shopwwi\Admin\App\User\Models\UserBalanceRecharge;
use Shopwwi\Admin\App\User\Models\UserBalanceRechargeMeal;

class BalanceRechargeService
{
    /**
     * 创建充值订单
     * @param $params
     * @param $userId
     * @return mixed
     * @throws \Exception
     */
    public static function rechargeAdd($params,$userId)
    {
        $data = [
            'user_id' => $userId,
            'amount' => 0,
            'pay_type' => 'RECHARGE',
            'pay_type_id' => 0,
            'pay_return' => "Shopwwi\\Admin\\Logic\\RechargePayLogic",
        ];
        $rechargeData = [
            'amount' => 0,
            'real_amount' => 0,
            'points' => 0,
            'growth' => 0,
            'user_id' => $userId,
            'status' => 0
        ];
        if(isset($params['mealId']) && !empty($params['mealId'])){ // 充值套餐处理
            $meal = UserBalanceRechargeMeal::where('id',$params['mealId'])->first();
            if($meal == null){
                throw new \Exception('充值套餐不存在');
            }
            $rechargeData['amount'] = $meal->price;
            $rechargeData['real_amount'] = $meal->amount;
            $rechargeData['points'] = $meal->point;
            $rechargeData['growth'] = $meal->growth;
            $rechargeData['meal_id'] = $meal->id;
        }else{ // 充值金额
            if(!isset($params['amount'])){
                throw new \Exception('充值金额不能为空');
            }
            $rechargeData['amount'] = $params['amount'];
            $rechargeData['real_amount'] = $params['amount'];
        }
        $recharge = UserBalanceRecharge::create($rechargeData);
        $data['amount'] = $recharge->amount;
        $data['pay_type_id'] = $recharge->id;
        $pay = SysPayService::create($data);
        $recharge->pay_sn = $pay->pay_sn;
        $recharge->save();
        return $pay;
    }

    public static function getIndexAmis()
    {
        $payStatus = DictTypeService::getAmisDictType('payStatus');
        return
            shopwwiAmis('crud')->perPage(15)
                ->perPageField('limit')
                ->bulkActions()->syncLocation(false)
                ->headerToolbar([
                    'bulkActions',
                    shopwwiAmis('reload')->align('right'),
                    self::getCreateAmis()
                ])
                ->api(shopwwiUserUrl('recharge?_format=json'))
                ->columns([
                    shopwwiAmis()->name('pay_sn')->label(trans('field.pay_sn',[],'userBalanceRecharge'))->searchable(shopwwiAmis('input-text')->name('pay_sn_like')->clearable(true))->sortable(true),
                    shopwwiAmis()->name('created_at')->label(trans('field.created_at',[],'messages'))->sortable(true),
                    shopwwiAmis()->name('amount')->label(trans('field.amount',[],'userBalanceRecharge'))->sortable(true),
                    shopwwiAmis()->name('real_amount')->label(trans('field.real_amount',[],'userBalanceRecharge')),
                    shopwwiAmis()->name('points')->label(trans('field.points',[],'userBalanceRecharge')),
                    shopwwiAmis()->name('growth')->label(trans('field.growth',[],'userBalanceRecharge')),
                    shopwwiAmis('select')->options($payStatus)->name('status')->static(true)->label(trans('field.status',[],'userBalanceRecharge'))->searchable(false)->filterable(['options'=>$payStatus])->sortable(true),
                    Operation::make()->label(trans('actions',[],'messages'))->fixed('right')->width(145)->buttons([
                        self::getShowAmis($payStatus)
                    ])
                ]);
    }

    /**
     * 新增提现申请
     * @return mixed
     */
    public static function getCreateAmis()
    {
        $list = UserBalanceRechargeMeal::orderBy('id','asc')->get();
        $options = [];
        $list->map(function ($item) use(&$options){
            $options[] = ['value' => $item->id, 'body' => "<div>\n  <div class=\"text-md text-center font-bold\">$item->amount 元</div>\n  <div class=\"text-sm\">售价：$item->price 元</div>\n         </div>"];
        });
        $options[] = ['value' => 0, 'body' => "<div>\n  <div class=\"text-md text-center font-bold\">\${amount}</div>\n  <div class=\"text-sm\">自定义金额</div>\n         </div>"];
        return shopwwiAmis('button')->actionType('dialog')->dialog(
            shopwwiAmis('dialog')->body(
                shopwwiAmis('form')->data(['amount' => 0])->panelClassName('must px-40 py-10 m:px-0 border-0 box-shadow-none')->mode('horizontal')
                    ->body([
                        shopwwiAmis('list-select')->name('mealId')->label('套餐')->required(true)->options($options),
                        shopwwiAmis('input-number')->name('amount')->label('充值金额')->required(true)->visibleOn('this.mealId === 0')->size('md')
                    ])->api('post:'.shopwwiUserUrl('recharge'))
            )->title('充值')->size('md')
        )->label('充值')->icon('ri-add-circle-line')->level('primary');
    }

    /**
     * 获取充值详情
     * @return mixed
     */
    public static function getShowAmis($payStatus)
    {
        return shopwwiAmis('button')->actionType('dialog')->dialog(
            shopwwiAmis('dialog')->body(
                shopwwiAmis('form')->panelClassName('must px-40 py-10 m:px-0 border-0 box-shadow-none')->mode('horizontal')
                    ->body([
                        shopwwiAmis('input-text')->name('pay_sn')->static(true)->label(trans('field.pay_sn',[],'userBalanceRecharge')),
                        shopwwiAmis('input-text')->name('amount')->static(true)->label(trans('field.amount',[],'userBalanceRecharge')),
                        shopwwiAmis('input-text')->name('real_amount')->static(true)->label(trans('field.real_amount',[],'userBalanceRecharge')),
                        shopwwiAmis('input-text')->name('points')->static(true)->label(trans('field.points',[],'userBalanceRecharge')),
                        shopwwiAmis('input-text')->name('growth')->static(true)->label(trans('field.growth',[],'userBalanceRecharge')),
                        shopwwiAmis('select')->options($payStatus)->name('status')->static(true)->label(trans('field.status',[],'userBalanceRecharge')),
                        shopwwiAmis('input-text')->name('created_at')->static(true)->label(trans('field.created_at',[],'messages')),

                    ])
            )->title('充值详情')
        )->label('详情')->icon('ri-eye-line');
    }
}