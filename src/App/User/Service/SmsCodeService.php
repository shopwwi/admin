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
use Shopwwi\Admin\App\Admin\Models\SysSmsCode;
use Shopwwi\Admin\App\User\Models\Users;

class SmsCodeService
{
    /**
     * 验证短信码
     * @param $mobile
     * @param $code
     * @param $type
     * @return void
     * @throws \Exception
     */
    public static function checkCode($mobile,$code,$type='LOGIN')
    {
        $has = SysSmsCode::where('mobile_phone',$mobile)
            ->where('auth_code',$code)
            ->where('send_type',$type)
            ->where('status',0)
            ->where('created_at','>',Carbon::now()->subMinutes(shopwwiConfig('sms.authCodeVerifyTime',5)))
            ->first();
        if($has == null){
            throw new \Exception('验证码不正确');
        }
        $has->status = 1;
        $has->save();
    }

    /**
     * 发送短信
     * @param $mobile
     * @param $type
     * @param $ip
     * @param $userId
     * @return void
     * @throws \Exception
     */
    public static function sendCode($mobile,$type,$ip,$userId)
    {
        //判断功能是否开启
        if(empty(shopwwiConfig('sms.used',0))){
            throw new \Exception("短信功能未开启");
        }

        //同一类型同一IP[n]秒内只能发一条短信
        if (self::repeatSendByIp($ip,$type,shopwwiConfig('sms.authCodeSameIpResendTime',60))) {
            throw new \Exception(shopwwiConfig('sms.authCodeSameIpResendTime',60) . "秒内，请勿多次获取短信码！");
        }
        //同一类型同一手机号[n]秒内只能发一条短信
        if (self::repeatSendByMobile($mobile,$type,shopwwiConfig('sms.authCodeResendTime',60))) {
            throw new \Exception(shopwwiConfig('sms.authCodeResendTime',60) . "秒内，请勿多次获取短信码！");
        }
        //同一手机号24小时内只能发[n]条短信
        if (self::sendOverNumByMobile($mobile,shopwwiConfig('sms.authCodeSameMaxNum',60))) {
            throw new \Exception("同一手机号24小时内，发送短信码次数不能超过" . shopwwiConfig('sms.authCodeSameMaxNum',60) . "次！");
        }
        //同一IP24小时内只能发[n]条短信
        if (self::sendOverNumByIp($ip,shopwwiConfig('sms.authCodeSameIpMaxNum',60))) {
            throw new \Exception("同一IP 24小时内，发送短信码次数不能超过".shopwwiConfig('sms.authCodeSameIpMaxNum',60) . "次！");
        }

        //查询会员信息
        $user = Users::where('phone',$mobile)->first();
        //注册、绑定手机时验证手机号是否已存在
        if ($type == 'register' && $user!=null) {
            throw new \Exception("当前手机号已存在，请更换其他号码");
        }
        if ($type == 'bind') {
            //此处判断userId是否相等是防止手机号存在但是未绑定造成无法绑定手机的情况，目前商城系统编辑手机同时必须绑定，该情况不存在。
            if($user != null && $userId != $user->id){
                throw new \Exception("当前手机号已被绑定，请更换其他号码");
            }
        }
        //登录、找回密码、安全验证时验证手机号是否存在
        if (in_array($type,['login','forget','auth'])) {
            if ($user == null || empty($user->mobile_bind)) {
                throw new \Exception("该手机号未绑定");
            }
        }
        //动态码
        $authCode = mt_rand(100000,999999);
        $logId = SysSmsCode::create([
            'send_type' => $type,
            'auth_code' => $authCode,
            'mobile_phone' => $mobile,
            'ip' => $ip
        ]);
        //发送短信
        SendMessageService::sendSystem($type.'Sms',$mobile,$authCode);
        return $logId;
    }

    /**
     * 同一IP是否重复发送动态码
     * @param $smsCode
     * @return bool
     */
    public static function repeatSendByIp($ip,$type,$num)
    {
        $count = SysSmsCode::where('ip',$ip)->where('send_type',$type)->where('created_at','>',Carbon::now()->subSeconds($num))->count();
        if($count > 0){
            return true;
        }
        return false;
    }

    /**
     * 同一手机号是否重复发送动态码
     * @param $mobile
     * @param $type
     * @param $num
     * @return bool
     */
    public static function repeatSendByMobile($mobile,$type,$num)
    {
        $count = SysSmsCode::where('mobile_phone',$mobile)->where('send_type',$type)->where('created_at','>',Carbon::now()->subSeconds($num))->count();
        if($count > 0){
            return true;
        }
        return false;
    }

    /**
     * 同一手机号24小时内只能发[n]条短信
     * @param $mobile
     * @param $num
     * @return bool
     */
    public static function sendOverNumByMobile($mobile,$num)
    {
        $count = SysSmsCode::where('mobile_phone',$mobile)->where('created_at','>',Carbon::now()->subDays(1))->count();
        if($count >= $num){
            return true;
        }
        return false;
    }

    /**
     * 同一IP24小时内只能发[n]条短信
     * @param $ip
     * @param $num
     * @return bool
     */
    public static function sendOverNumByIp($ip,$num)
    {
        $count = SysSmsCode::where('ip',$ip)->where('created_at','>',Carbon::now()->subDays(1))->count();
        if($count >= $num){
            return true;
        }
        return false;
    }
}