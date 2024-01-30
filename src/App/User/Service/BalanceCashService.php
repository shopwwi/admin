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

use Carbon\Carbon;
use Shopwwi\Admin\Amis\Operation;
use Shopwwi\Admin\App\Admin\Service\DictTypeService;
use Shopwwi\Admin\App\Admin\Service\SysConfigService;
use Shopwwi\Admin\App\Admin\Service\User\BalanceService;
use Shopwwi\Admin\App\User\Models\UserBalanceCash;
use Shopwwi\Admin\App\User\Models\UserCard;
use Shopwwi\Admin\App\User\Models\Users;
use support\Db;

class BalanceCashService
{
    /**
     * 申请提现
     * @param $amount
     * @param $bankId
     * @param $userId
     * @throws \Exception
     */
    public static function addCash($amount,$bankId,$userId)
    {
        $config = shopwwiConfig('cash');
        if(empty($config['used'])){
            throw new \Exception('提现功能未开启');
        }
        $rule = $config['rule'] ?? ['time' => 1, 'num'=> 99];
        // 提现周期次数计算
        $count = UserBalanceCash::where('user_id',$userId)->whereIn('cash_status',[0,1])->whereBetween('created_at',[Carbon::now()->subDays($rule['time']),Carbon::now()])->count();
        if(\bccomp($count,$rule['num']) == 1){
            throw new \Exception('周期可提现次数不足');
        }

        $amount = \bcmul($amount,1,2); // 精确小数点两位
        if(\bccomp($amount,$config['min']) == -1 ){
            throw new \Exception('提现金额应大于'.$config['min']);
        }
        $user = Users::where('id',$userId)->first();
        if($user == null){
            throw new \Exception('会员信息异常');
        }
        if(\bccomp($amount,$user->available_balance) == 1 || \bccomp($amount,$config['max']) == 1){
            throw new \Exception('可提现金额不足');
        }
        $bankInfo = UserCard::where('id',$bankId)->where('user_id',$userId)->first();
        if($bankInfo == null){
            throw new \Exception('绑卡信息异常');
        }
        // 生成提现编号
        $sn = self::getCashSn($userId);
        if($sn == ''){
            throw new \Exception(trans('error',[],'messages'));
        }
        $serviceAmount = \bcdiv(\bcmul($amount , $config['rate']),100,2);
        $realAmount = bcsub($amount,$serviceAmount,2);
        try {
            Db::beginTransaction();
            // 提现申请日志
            BalanceService::addLogCashApply($amount,$userId,$sn);

            $userCash = UserBalanceCash::create([
                'cash_sn' => $sn,
                'cash_amount' => $amount,
                'service_amount' => $serviceAmount,
                'amount' => $realAmount,
                'user_id' => $userId,
                'cash_status' => '0',
                'bank_name' => $bankInfo->bank_name,
                'bank_account' => $bankInfo->bank_account,
                'bank_username' => $bankInfo->bank_username,
                'bank_branch' => $bankInfo->bank_branch,
                'bank_type' => $bankInfo->bank_type,
            ]);
            Db::commit();
            // 发送消息
            //SendMessageService::sendUser('userCashApply',$userId,['cashSn'=>$userCash->cash_sn,'amount'=>$amount,'addTime' => date('Y-m-d H:i:s')],$userCash->id);
        }catch (\Exception $e){
            Db::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * 取消提现
     * @param $cashId
     * @param $userId
     * @throws \Exception
     */
    public static function cancelCash($cashId,$userId)
    {
        try {
            Db::beginTransaction();
            $userCash = UserBalanceCash::where('user_id',$userId)->where('id',$cashId)->lockForUpdate()->first();
            if($userCash == null){
                throw new \Exception('提现不存在');
            }
            if($userCash->cash_status != '0'){
                throw new \Exception('该状态不允许取消');
            }
            // 取消提现余额日志
            BalanceService::addLogCashDel($userCash->cash_amount,$userId,$userCash->cash_sn);

            // 写入提现日志
            $userCash->cash_status = '8';
            $userCash->save();
            Db::commit();
            // 发送消息
            //SendMessageService::sendUser('userCashCancel',$userId,['cashSn'=>$userCash->cash_sn,'addTime' => date('Y-m-d H:i:s')],$userCash->id);
        }catch (\Exception $e){
            Db::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * 生成提现单号
     * @param $userId
     * @return string
     */
    public static function getCashSn($userId)
    {
        $snAble = false;
        $sn = "";
        //验证该单号是否存在 最多循环100次
        for($i = 0; $i<100; $i++) {
            if ($snAble == true) {
                break;
            }
            $sn = mt_rand(10000,99999).sprintf('%03d', (int) $userId % 1000).sprintf('%010d',time() - 946656000);
            $info = UserBalanceCash::where('cash_sn',$sn)->first();
            if ($info == null) {
                $snAble = true;
            }else{
                $sn = "";
            }
        }
        return $sn;
    }

    /**
     * 获取提现列表
     * @param $userId
     * @return mixed
     */
    public static function getIndexAmis($userId)
    {
        $cashStatus = DictTypeService::getAmisDictType('cashStatus');
        return
            shopwwiAmis('crud')->perPage(15)
                ->perPageField('limit')
                ->bulkActions()->syncLocation(false)
                ->headerToolbar([
                    'bulkActions',
                    shopwwiAmis('reload')->align('right'),
                    self::getCreateAmis($userId)
                ])
                ->api(shopwwiUserUrl('cash?_format=json'))
                ->columns([
                    shopwwiAmis()->name('cash_sn')->label(trans('field.cash_sn',[],'userBalanceCash'))->sortable(true)->searchable(shopwwiAmis('input-text')->name('cash_sn_like')->clearable(true)),
                    shopwwiAmis()->name('created_at')->label(trans('field.created_at',[],'messages'))->sortable(true),
                    shopwwiAmis()->name('cash_amount')->label(trans('field.cash_amount',[],'userBalanceCash'))->sortable(true),
                    shopwwiAmis()->name('service_amount')->label(trans('field.service_amount',[],'userBalanceCash')),
                    shopwwiAmis()->name('amount')->label(trans('field.amount',[],'userBalanceCash')),
                    shopwwiAmis('select')->options($cashStatus)->name('cash_status')->static(true)->label(trans('field.cash_status',[],'userBalanceCash'))->searchable(false)->filterable(['options'=>$cashStatus])->sortable(true),
                    Operation::make()->label(trans('actions',[],'messages'))->fixed('right')->width(145)->buttons([
                        self::getShowAmis($cashStatus)
                    ])
                ]);
    }

    /**
     * 新增提现申请
     * @param $userId
     * @return mixed
     */
    public static function getCreateAmis($userId)
    {
        $bankList = [];
        $bankListMap = UserCard::where('user_id',$userId)->get();
        $bankListMap->map(function ($item) use (&$bankList){
            $bankList[] = ['label'=>$item->bank_name,'value'=>$item->id];
        });
        return shopwwiAmis('button')->actionType('dialog')->dialog(
            shopwwiAmis('dialog')->body(
                shopwwiAmis('form')->panelClassName('must px-40 py-10 m:px-0 border-0 box-shadow-none')->mode('horizontal')
                ->body([
                    shopwwiAmis('select')->name('bankId')->label(trans('field.bankId',[],'userBalanceCash'))->options($bankList)->required(true),
                    shopwwiAmis('input-number')->name('amount')->min(0.01)->precision(2)->label(trans('field.cash_amount',[],'userBalanceCash'))->placeholder(trans('form.input',['attribute'=>trans('field.cash_amount',[],'userBalanceCash')],'messages'))->required(true),
                ])->api('post:'.shopwwiUserUrl('cash'))
            )->title('提现')
        )->label('提现')->icon('ri-add-circle-line')->level('primary');
    }

    /**
     * 获取提现详情
     * @return mixed
     */
    public static function getShowAmis($cashStatus)
    {
        return shopwwiAmis('button')->actionType('dialog')->dialog(
            shopwwiAmis('dialog')->body(
                shopwwiAmis('form')->panelClassName('must px-40 py-10 m:px-0 border-0 box-shadow-none')->mode('horizontal')
                    ->body([
                        shopwwiAmis('input-text')->name('cash_sn')->static(true)->label(trans('field.cash_sn',[],'userBalanceCash')),
                        shopwwiAmis('input-text')->name('cash_amount')->static(true)->label(trans('field.cash_amount',[],'userBalanceCash')),
                        shopwwiAmis('input-text')->name('service_amount')->static(true)->label(trans('field.service_amount',[],'userBalanceCash')),
                        shopwwiAmis('input-text')->name('amount')->static(true)->label(trans('field.amount',[],'userBalanceCash')),
                        shopwwiAmis('input-text')->name('bank_name')->static(true)->label(trans('field.bank_name',[],'userBalanceCash')),
                        shopwwiAmis('input-text')->name('bank_account')->static(true)->label(trans('field.bank_account',[],'userBalanceCash')),
                        shopwwiAmis('input-text')->name('bank_username')->static(true)->label(trans('field.bank_username',[],'userBalanceCash')),
                        shopwwiAmis('input-text')->name('bank_branch')->static(true)->label(trans('field.bank_branch',[],'userBalanceCash')),
                        shopwwiAmis('select')->name('bank_type')->static(true)->label(trans('field.bank_type',[],'userBalanceCash'))->options(DictTypeService::getAmisDictType('userCardType')),
                        shopwwiAmis('select')->name('cash_status')->static(true)->label(trans('field.cash_status',[],'userBalanceCash'))->options($cashStatus),
                    ])
            )->title('提现详情')
        )->label('详情')->icon('ri-eye-line');
    }

}