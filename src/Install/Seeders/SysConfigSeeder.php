<?php

namespace Shopwwi\Admin\Install\Seeders;

use Shopwwi\Admin\App\Admin\Models\SysConfig;

class SysConfigSeeder
{
    public static function run()
    {
        SysConfig::query()->truncate();
        $list = self::getData();
        foreach ($list as $item){
            SysConfig::create($item);
        }
    }

    protected static function getData(){
        return [
            ['key'=>'siteInfo','name' => '站点信息','value' => [
                'siteName' => '',
                'siteIcp' => '',
                'siteLogo' => '/static/uploads/common/logo.svg',
                'siteIcon' => '',
                'siteKeyword' => '',
                'siteDescription' => '',
                'siteStatus' => '0',
                'siteCloseRemark' => '',
                'siteEmail' => '',
                'sitePhone' => '',
                'siteFlowCode' => ''
            ],'is_system' => '1','is_open' => '1'],
            ['key'=>'siteImages','name' => '默认图片配置','value' => [
                'userAvatar' => '/static/uploads/common/user-avatar.png', // 用户默认头像
                'noPic' => '', //默认图片
            ],'is_system' => '1','is_open' => '1'],
            ['key'=>'siteAuthRule','name' => '站点规则设置','value' => [
                'authCodeVerifyTime' => 5, // 验证有效时间 分钟
                'authCodeResendTime' => 60, // 同一类型同一手机号/邮箱[n]秒内只能发一条验证码
                'authCodeSameIpResendTime' => 30, // 同一类型同一IP[n]秒内只能发一条验证码
                'authCodeSameIpEmailResendTime' => 5,// 同一类型同一IP[n]秒内只能发一条邮件
                'authCodeSamePhoneMaxNum' => 12, // 同一手机号24小时内，发送验证码次数不能超过
                'authCodeSameEmailMaxNum' => 50, // 同一邮箱24小时内，发送验证码次数不能超过
                'authCodeSameEmailIpMaxNum' => 3, // 同一IP 24小时内，邮箱发送动态码次数不能超过
                'authCodeSameIpMaxNum' => 3, // 同一IP 24小时内，发送动态码次数不能超过
            ],'is_system' => '1','is_open' => '1'],
            ['key'=>'sms','name' => '短信服务','value' => [
                'used'=>'0',
                'timeout' => 5.0,
                'gateways'=>[
                    'yunpian' => [
                        'api_key' => '824f0ff2f71cab52936axxxxxxxxxx',
                        'signature' => '【默认签名】'
                    ],
                    'aliyun' => [
                        'access_key_id' => '',
                        'access_key_secret' => '',
                        'sign_name' => '',
                    ],
                ],
                "authCodeVerifyTime" => 5,
                "authCodeResendTime" => 60,
                "authCodeSameIpResendTime" => 180,
                "authCodeSameMaxNum" => 20,
                "authCodeSameIpMaxNum" => 20
            ],'is_system' => '1','is_open' => '0'],
            ['key'=>'email','name' => '邮件服务','value' => [
                'used'=>'1',
                'username'=>'',
                'password'=>'',
                'encryption'=>'ssl',
                'port' => '25',
                'smtp' => 'smtp.qq.com',
                'form_address' => '',
                'form_name' => '',
                "authCodeVerifyTime" => 20,
                "authCodeResendTime" => 60,
                "authCodeSameIpResendTime" => 180,
                "authCodeSameMaxNum" => 20,
                "authCodeSameIpMaxNum" => 20
            ],'is_system' => '1','is_open' => '0'],
            ['key'=>'express','name' => '快递查询','value' => [
                'used' => '0',
                'type' => 'kdniao',
                'appId' => '',
                'appKey' => '',
            ],'is_system' => '1','is_open' => '0'],
            ['key'=>'growth','name' => '成长值规则','value' => [
                'used' => '0',
                'rules' => [
                    ['label'=>'register','value'=>5,'desc'=>'当会员注册成功后将获得相应的成长值'],
                    ['label'=>'login','value'=>5,'desc'=>'当会员每天第一次登录成功后将获得相应的成长值']
                ],
            ],'is_system' => '1','is_open' => '0'],
            ['key'=>'point','name' => '积分规则设置','value' => [
                'used' => '0',
                'rules' => [
                    ['label'=>'register','value'=>5,'desc'=>'该值为大于等于0的数，当会员注册成功后将获得相应的积分'],
                    ['label'=>'login','value'=>5,'desc'=>'该值为大于等于0的数，当会员每天第一次登录成功后将获得相应的积分']
                ],
            ],'is_system' => '1','is_open' => '0'],
            ['key'=>'map','name' => '地图服务设置','value' => [
                'type' => 'gaode',
                'baiduKey' => '',
                'amapKey' => '',
                'qqKey' => '',
            ],'is_system' => '1','is_open' => '0'],
            ['key'=>'cash','name' => '提现设置','value' => [
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
            ],'is_system' => '1','is_open' => '0'],
        ];
    }
}