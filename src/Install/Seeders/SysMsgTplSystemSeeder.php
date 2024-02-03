<?php

namespace Shopwwi\Admin\Install\Seeders;

use Shopwwi\Admin\App\Admin\Models\SysMsgTplSystem;

class SysMsgTplSystemSeeder
{
    public static function run()
    {
        $list = self::data();
        foreach ($list as $item){
            SysMsgTplSystem::updateOrCreate(['code'=>$item['code']],$item);
        }
    }

    protected static function data(){
        return [
            ['code'=>'authEmail','send_type'=>'EMAIL','content'=>'【{$siteName}】您正在进行身份安全验证，验证码是：{$code}。','name'=>'<strong>[用户]</strong>邮箱身份安全验证','title' =>'邮箱身份安全验证通知 - {$siteName}'],
            ['code'=>'authMobile','send_type'=>'MSG','content'=>'【{$siteName}】您正在进行身份安全验证，验证码是：{$code}。','name'=>'<strong>[用户]</strong>手机身份安全验证','title' =>''],
            ['code'=>'bindEmail','send_type'=>'EMAIL','content'=>'【{$siteName}】您正在绑定邮箱，验证码是：{$code}。','name'=>'<strong>[用户]</strong>邮箱绑定','title' =>'账户绑定邮箱验证通知 - {$siteName}'],
            ['code'=>'bindMobile','send_type'=>'MSG','content'=>'【{$siteName}】您正在绑定手机，验证码是：{$code}。','name'=>'<strong>[用户]</strong>手机绑定','title' =>''],
            ['code'=>'findPasswordEmail','send_type'=>'EMAIL','content'=>'【{$siteName}】<p>您正在通过邮箱找回密码，验证码是：{$code}。</p>','name'=>'<strong>[用户]</strong>邮箱找回密码','title' =>'邮箱找回密码通知 - {$siteName}'],
            ['code'=>'findPasswordMobile','send_type'=>'MSG','content'=>'【{$siteName}】您正在通过手机号找回密码，验证码是：{$code}。','name'=>'<strong>[用户]</strong>手机找回密码','title' =>''],
            ['code'=>'loginMobile','send_type'=>'MSG','content'=>'【{$siteName}】您正在通过手机登录，验证码是：{$code}。','name'=>'<strong>[用户]</strong>手机登录','title' =>''],
            ['code'=>'registerMobile','send_type'=>'MSG','content'=>'【{$siteName}】您正在通过手机注册会员，验证码是：{$code}。','name'=>'<strong>[用户]</strong>手机注册','title' =>''],
        ];
    }
}