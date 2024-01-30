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
namespace Shopwwi\Admin\App\Admin\Service;

use Shopwwi\Admin\App\Admin\Models\SysPay;
use Shopwwi\Admin\App\Admin\Models\SysPayment;
use Shopwwi\Admin\Logic\PayLogic;
use Shopwwi\LaravelCache\Cache;

class SysPayService
{
    /**
     * 创建支付单
     * @param $params
     * @return mixed
     */
    public static function create($params)
    {
        $data = [
            'pay_sn' => self::getPaySn($params['user_id']),
            'amount' => $params['amount'],
            'status' => '0',
            'pay_type' => $params['pay_type'],
            'pay_type_id' => $params['pay_type_id'],
            'pay_return' => $params['pay_return'],
            'user_id' => $params['user_id']
        ];
        if(!empty($params['payment_name']) && !empty($params['payment_code'])){
            $data['payment_name'] = $params['payment_name'];
            $data['payment_code'] = $params['payment_code'];
        }
        return SysPay::create($data);
    }


    /**
     * 生成支付单号
     * 生成规则：两位随机 + 从2000-01-01 00:00:00 到现在的秒数+三位随机+会员ID%1000，该值会传给第三方支付接口
     * 长度 =2位 + 10位 + 3位 + 3位  = 18位
     * @param $userId
     * @return string
     */
    public static function getPaySn($userId): string
    {
        return mt_rand(10,99)
            . sprintf('%010d',time() - 946656000)
            . sprintf('%03d', (float) microtime() * 1000)
            . sprintf('%03d', (int) $userId % 1000);
    }

    /**
     * 修改支付单信息
     * @param $paySn
     * @param $params
     * @throws \Exception
     */
    public static function updateInfo($paySn,$params)
    {
        $payInfo = SysPay::where('pay_sn',$paySn)->first();
        if($payInfo == null){
            throw new \Exception('支付订单不存在');
        }
        foreach ($params as $key=>$val){
            $payInfo->$key = $val;
        }
        if($payInfo->amount == 0 && empty($payInfo->status)){
            $payInfo->status = 1;
            $payInfo->pay_time = now();
            $payInfo->save();
            PayLogic::adminPay($payInfo);
        }else{
            $payInfo->save();
        }
    }

    /**
     * 获取支付方式列表
     * @return mixed
     */
    public static function getPaymentList(){
        return Cache::rememberForever('shopwwiSysPayment', function () {
            return SysPayment::get();
        });
    }

    /**
     * 获取在线支付方式列表
     * @param $clientType
     * @return mixed
     */
    public static function getOnlinePaymentList($clientType){
        $list = self::getPaymentList();
        return $list->where('status',1)->where($clientType,1)->whereNotIn('code',['offline','balance'])->diffKeys(['config'=>''])->values();
    }

    /**
     * 清除缓存
     * @return void
     */
    public static function clear(){
        Cache::forget("shopwwiSysPayment");
    }
}