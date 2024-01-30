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

namespace Shopwwi\Admin\Logic;

use Shopwwi\Admin\App\Admin\Service\User\BalanceService;
use Shopwwi\Admin\App\Admin\Service\User\GrowthService;
use Shopwwi\Admin\App\Admin\Service\User\PointService;
use Shopwwi\Admin\App\User\Models\UserBalanceRecharge;
use Shopwwi\Admin\Libraries\PayInterface;

class RechargePayLogic implements PayInterface
{
    /**
     * 处理
     * @param $payInfo
     * @throws \Exception
     */
    public function handle($payInfo)
    {
        $itemId = $payInfo->pay_type_id;
        $recharge = UserBalanceRecharge::where('id',$itemId)->first();
        if($recharge == null){
            throw new \Exception('充值订单不存在');
        }
        // 赠送积分
        if($recharge->points > 0){
            PointService::addLog($recharge->user_id,'INCREASE',$recharge->points,[
                'description' => '充值余额活动,获得积分',
                'user_id' => $recharge->user_id,
                'operation_stage' => 'ACTIVITY',
                'points' => $recharge->points,
            ]);
        }

        // 赠送经验值
        if($recharge->growth > 0){
            GrowthService::addLog($recharge->user_id,'INCREASE',$recharge->growth,[
                'description' => '充值余额活动,获得成长值',
                'user_id' => $recharge->user_id,
                'operation_stage' => 'ACTIVITY',
                'growth' => $recharge->growth,
            ]);
        }
        // 资金入库
        BalanceService::addLog($recharge->user_id,'INCREASE',$recharge->real_amount,[
            'description' => '充值成功，充值单号：'.$recharge->pay_sn,
            'user_id' => $recharge->user_id,
            'operation_stage' => 'RECHARGE',
            'available_balance' => $recharge->real_amount,
            'frozen_balance' => 0,
            'old_amount' => 0,
        ]);

    }
}