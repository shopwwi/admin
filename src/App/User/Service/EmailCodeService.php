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
use Shopwwi\Admin\App\Admin\Models\SysEmailCode;
use Shopwwi\Admin\App\User\Models\Users;

class EmailCodeService
{
    /**
     * 邮箱验证
     * @param $email
     * @param $code
     * @param string $type
     * @throws \Exception
     */
    public static function checkCode($email,$code,$type='login')
    {
        $has = SysEmailCode::where('email',$email)
            ->where('auth_code',$code)
            ->where('send_type',$type)
            ->where('created_at','>',Carbon::now()->subMinutes(shopwwiConfig('email.authCodeVerifyTime',20)))
            ->where('status',0)
            ->first();
        if($has == null){
            throw new \Exception('验证码不正确');
        }
        $has->status = 1;
        $has->save();
    }

    /**
     * 发送邮件信息
     * @param $email
     * @param $type
     * @param $ip
     * @param $userId
     * @return mixed
     * @throws \Exception
     */
    public static function sendCode($email,$type,$ip,$userId)
    {
        //判断功能是否开启
        if(empty(shopwwiConfig('email.used',0))){
            throw new \Exception("邮件功能未开启");
        }

        //同一类型同一IP[n]秒内只能发一封邮件
        if (self::repeatSendByIp($ip,$type,shopwwiConfig('email.authCodeSameIpResendTime',60))) {
            throw new \Exception(shopwwiConfig('email.authCodeSameIpResendTime',60) . "秒内，请勿多次获取验证码！");
        }
        //同一类型同一邮箱[n]秒内只能发一封邮件
        if (self::repeatSendByEmail($email,$type,shopwwiConfig('email.authCodeResendTime',60))) {
            throw new \Exception("同一邮箱" .shopwwiConfig('email.authCodeResendTime',60) . "秒内，请勿多次获取验证码！");
        }
        //同一邮箱24小时内只能发[n]封邮件
        if (self::sendOverNumByEmail($email,shopwwiConfig('email.authCodeSameMaxNum',60))) {
            throw new \Exception("同一邮箱24小时内，发送动态码次数不能超过" . shopwwiConfig('email.authCodeSameMaxNum',60) . "次！");
        }
        //同一IP24小时内只能发[n]封邮件
        if (self::sendOverNumByIp($ip,shopwwiConfig('email.authCodeSameIpMaxNum',100))) {
            throw new \Exception("同一IP 24小时内，发送动态码次数不能超过". shopwwiConfig('email.authCodeSameIpMaxNum',100) . "次！");
        }

        //查询会员信息
        $user = Users::where('email',$email)->first();
        //注册、绑定邮箱时验证邮箱是否已存在
        if ($type == 'register' && $user!=null) {
            throw new \Exception("当前邮箱已存在，请更换其他号码");
        }
        if($type == 'bind'){
            if($user != null && $userId != $user->id){
                throw new \Exception("当前邮箱已被占用，请更换其他邮箱");
            }
        }
        //登录、找回密码、安全验证时验证手机号是否存在
        if(in_array($type,['login','forget','auth'])){
            if($user == null || $user->email_bind  < 1){
                throw new \Exception("该邮箱未绑定");
            }
        }
        //动态码
        $authCode = mt_rand(100000,999999);
        $logId = SysEmailCode::create([
            'send_type' => $type,
            'auth_code' => $authCode,
            'email' => $email,
            'ip' => $ip
        ]);
        //发送邮件
        SendMessageService::sendSystem($type.'Email',$email,$authCode);
        return $logId;
    }

    /**
     * 同一IP是否重复发送动态码
     * @param $emailCode
     * @return bool
     */
    public static function repeatSendByIp($ip,$type,$num)
    {
        $count = SysEmailCode::where('ip',$ip)->where('send_type',$type)->where('created_at','>',now()->subSeconds($num))->count();
        if($count > 0){
            return true;
        }
        return false;
    }

    /**
     * 同一邮箱是否重复发送动态码
     * @param $emailCode
     * @return bool
     */
    public static function repeatSendByEmail($email,$type,$num)
    {
        $count = SysEmailCode::where('email',$email)->where('send_type',$type)->where('created_at','>',Carbon::now()->subSeconds($num))->count();
        if($count > 0){
            return true;
        }
        return false;
    }

    /**
     * 同一邮箱24小时内只能发[n]封邮件
     * @param $emailCode
     * @return bool
     */
    public static function sendOverNumByEmail($email,$num)
    {
        $count = SysEmailCode::where('email',$email)->where('created_at','>',Carbon::now()->subDays(1))->count();
        if($count >= $num){
            return true;
        }
        return false;
    }

    /**
     * 同一IP24小时内只能发[n]封邮件
     * @param $emailCode
     * @return bool
     */
    public static function sendOverNumByIp($ip,$num)
    {
        $count = SysEmailCode::where('ip',$ip)->where('created_at','>',Carbon::now()->subDays(1))->count();
        if($count >= $num){
            return true;
        }
        return false;
    }
}