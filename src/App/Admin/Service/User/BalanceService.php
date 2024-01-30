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
namespace Shopwwi\Admin\App\Admin\Service\User;

use Carbon\Carbon;
use Shopwwi\Admin\App\User\Models\UserBalanceCash;
use Shopwwi\Admin\App\User\Models\UserBalanceLog;
use Shopwwi\Admin\App\User\Models\Users;
use Shopwwi\Admin\App\User\Service\SendMessageService;
use support\Db;

class BalanceService
{
    /**
     * 管理员操作余额增减
     * @param $userId
     * @param $trimType
     * @param $num
     * @param $adminId
     * @param $adminName
     * @param string $cause
     * @return void
     * @throws \Exception
     */
    public static function adminTrim($userId,$trimType,$num,$adminId,$adminName,$cause='')
    {
        $trimType = strtoupper($trimType);
        $available = 0;
        $frozen = 0;
        $description = '';
        switch ($trimType){
            case 'INCREASE':
                $available = $num;
                $description = '系统变更余额，增加余额 '.$num;
                break;
            case 'DECREASE':
                $available = -$num;
                $description = '系统变更余额，减少余额 '.$num;
                break;
            case 'FREEZE':
                $available = -$num;
                $frozen = $num;
                $description = '系统冻结余额，减少可用余额 '.$num.' ，增加冻结余额 '.$num;
                break;
            case 'UNFREEZE':
                $available = $num;
                $frozen = -$num;
                $description = '系统解冻余额，增加可用余额 '.$num.' ，减少冻结余额 '.$num;
                break;
        }

        $log = [
            'sys_user_id' => $adminId,
            'sys_user_name' => $adminName,
            'old_amount' => 0,
            'available_balance' => $available,
            'frozen_balance' => $frozen,
            'description' => $description.(!empty($cause)?"（操作原因：{$cause}）":''),
            'user_id' => $userId,
            'operation_stage' => 'SYSTEM',
        ];
        self::addLog($userId,$trimType,$num,$log);
    }

    /**
     * 预存款申请提现，冻结预存款
     * @param $amount
     * @param $userId
     * @param $cashSn
     * @return void
     * @throws \Exception
     */
    public static function addLogCashApply($amount,$userId,$cashSn){
        $log = [
            'old_amount' => 0,
            'available_balance' => -$amount,
            'frozen_balance' => $amount,
            'description' => '申请提现，冻结预存款，提现单号:'.$cashSn,
            'user_id' => $userId,
            'operation_stage' => 'CASH',
        ];
        // 写入日志
        self::addLog($userId,'FREEZE',$amount,$log);
    }

    /**
     * 取消提现
     * @param $amount
     * @param $userId
     * @param $cashSn
     * @return void
     * @throws \Exception
     */
    public static function addLogCashDel($amount,$userId,$cashSn){
        $log = [
            'old_amount' => 0,
            'available_balance' => $amount,
            'frozen_balance' => -$amount,
            'description' => '取消提现，提现单号：'.$cashSn,
            'user_id' => $userId,
            'operation_stage' => 'CASH',
        ];
        // 写入日志
        self::addLog($userId,'UNFREEZE',$amount,$log);
    }


    /**
     * 管理员操作提现
     * @param $params
     * @param $cashId
     * @param $adminId
     * @param $adminName
     * @return mixed
     * @throws \Exception
     */
    public static function adminTrimCash($params,$cashId,$adminId,$adminName)
    {
        try {
            Db::beginTransaction();
            $cashInfo = UserBalanceCash::where('id', $cashId)->lockForUpdate()->first();
            if($cashInfo == null){
                throw new \Exception(trans('dataError',[],'messages'));
            }
            if (!empty($cashInfo->cash_status)) {
                throw new \Exception('已经处理过了');
            }

            if(!in_array($params['cash_status'],[1,2])){
                    throw new \Exception(trans('dataError',[],'messages'));
            }
            switch ($params['cash_status']) {
                case 1:
                    $log = [
                        'old_amount' => 0,
                        'available_balance' => 0,
                        'frozen_balance' => -$cashInfo->cash_amount,
                        'description' => '提现成功，提现单号：' . $cashInfo->cash_sn,
                        'user_id' => $cashInfo->user_id,
                        'operation_stage' => 'CASH',
                    ];
                    self::addLog($cashInfo->user_id,'SUBFREEZE',$cashInfo->cash_amount,$log);
                    break;
                case 2:
                    $log = [
                        'old_amount' => 0,
                        'available_balance' => $cashInfo->cash_amount,
                        'frozen_balance' => -$cashInfo->cash_amount,
                        'description' => '平台拒绝提现，提现单号:' . $cashInfo->cash_sn,
                        'user_id' => $cashInfo->user_id,
                        'operation_stage' => 'CASH',
                    ];
                    self::addLog($cashInfo->user_id,'UNFREEZE',$cashInfo->cash_amount,$log);
                    break;
            }
            foreach ($params as $key=>$val){
                $cashInfo->$key = $val;
            }
            $cashInfo->sys_user_id = $adminId;
            $cashInfo->sys_user_name = $adminName;
            $cashInfo->save();
            Db::commit();
            if($params['cash_status'] == 2){
                SendMessageService::sendUser('userBalanceCashFail',$cashInfo->user_id,['cashSn'=>$cashInfo->cash_sn,'createdAt'=>date('Y-m-d H:i:s')],$cashInfo->id);
            }

            return $cashInfo;
        }catch (\Exception $e){
            Db::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * 写入日志及变更处理
     * @param $userId
     * @param $trimType
     * @param $num
     * @param $log
     * @throws \Exception
     */
    public static function addLog($userId,$trimType,$num,$log)
    {
        $trimType = strtoupper($trimType);
        $user = Users::where('id',$userId)->lockForUpdate()->first();
        if($user == null){
            throw new \Exception('会员不存在');
        }

        if(!in_array($trimType,['INCREASE','DECREASE','FREEZE','UNFREEZE','SUBFREEZE'])){
            throw new \Exception('操作类型错误');
        }
        $log['user_name'] = $user->nickname;
        $log['old_amount'] = \bcadd($user->available_balance,$user->frozen_balance,2);
        switch ($trimType){
            case 'INCREASE':
                $user->available_balance = Db::raw('available_balance +'.$num);
                $user->balance = Db::raw('balance +'.$num);
                break;
            case 'DECREASE':
                if($user->available_balance < $num){
                    throw new \Exception('可操作可用金额不足');
                }
                $user->available_balance = Db::raw('available_balance -'.$num);
                break;
            case 'FREEZE':
                if($user->available_balance < $num){
                    throw new \Exception('可操作可用金额不足');
                }
                $user->available_balance = Db::raw('available_balance -'.$num);
                $user->frozen_balance = Db::raw('frozen_balance +'.$num);
                break;
            case 'UNFREEZE':
                if($user->frozen_balance < $num){
                    throw new \Exception('可操作解冻金额不足');
                }
                $user->available_balance = Db::raw('available_balance +'.$num);
                $user->frozen_balance = Db::raw('frozen_balance -'.$num);
                break;
            case 'SUBFREEZE':
                if($user->frozen_balance < $num){
                    throw new \Exception('可操作解冻金额不足');
                }
                $user->frozen_balance = Db::raw('frozen_balance -'.$num);
                break;
        }
        $user->save();
        // 写入积分日志
        $logs = UserBalanceLog::create($log);
        //发送消息
        SendMessageService::sendUser('userBalanceChange',$userId,['amount'=>$num,'addTime'=>now()->format('Y-m-d H:i:s')],$logs->id);
    }
}