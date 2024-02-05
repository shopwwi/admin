<?php

namespace Shopwwi\Admin\Install\Seeders;

use Shopwwi\Admin\App\Admin\Models\SysConfig;
use support\Request;

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
                'siteName' => 'ShopWWI智能管理系统',
                'siteUrl' => '',
                'siteIcp' => '',
                'siteLogo' => 'uploads/common/logo.svg',
                'siteIcon' => '',
                'siteKeyword' => '',
                'siteDescription' => '',
                'siteStatus' => '1',
                'siteCloseRemark' => '',
                'siteEmail' => '',
                'sitePhone' => '',
                'siteFlowCode' => ''
            ],'is_system' => '1','is_open' => '1'],
            ['key'=>'siteDefaultImage','name' => '默认图片配置','value' => [
                'userAvatar' => 'uploads/default-avatar.png', // 用户默认头像
                'noPic' => 'uploads/default-image.png', //默认图片
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
                'type' => 'amap',
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
            ['key'=>'socialite','name' => '登入信息配置','value' => [
                'qq' => ['client_id'=>'','client_secret'=>'','redirect'=>'{$userUrl}/auth/qq/callback'],
                'wechat' => ['client_id'=>'','client_secret'=>'','component'=>['id'=>'','token'=>''],'redirect'=>'{$userUrl}/auth/wechat/callback'],
                'weibo' => ['client_id'=>'','client_secret'=>'','redirect'=>'{$userUrl}/auth/weibo/callback'],
                'taobao' => ['client_id'=>'','client_secret'=>'','redirect'=>'{$userUrl}/auth/taobao/callback'],
                'alipay' => ['client_id'=>'','rsa_private_key'=>'','redirect'=>'{$userUrl}/auth/alipay/callback'],
                'coding' => ['client_id'=>'','client_secret'=>'','team_url'=>'https://{your-team}.coding.net', 'redirect'=>'{$userUrl}/auth/coding/callback'],
                'dingtalk' => ['client_id'=>'','client_secret'=>'', 'redirect'=>'{$userUrl}/auth/dingtalk/callback'],
                'baidu' => ['client_id'=>'','client_secret'=>'', 'redirect'=>'{$userUrl}/auth/baidu/callback'],
                'azure' => ['client_id'=>'','client_secret'=>'', 'redirect'=>'{$userUrl}/auth/azure/callback'],
                'douban' => ['client_id'=>'','client_secret'=>'', 'redirect'=>'{$userUrl}/auth/douban/callback'],
                'facebook' => ['client_id'=>'','client_secret'=>'', 'redirect'=>'{$userUrl}/auth/facebook/callback'],
                'feishu' => ['client_id'=>'','client_secret'=>'', 'redirect'=>'{$userUrl}/auth/feishu/callback'],
                'figma' => ['client_id'=>'','client_secret'=>'', 'redirect'=>'{$userUrl}/auth/figma/callback'],
                'gitee' => ['client_id'=>'','client_secret'=>'', 'redirect'=>'{$userUrl}/auth/gitee/callback'],
                'github' => ['client_id'=>'','client_secret'=>'', 'redirect'=>'{$userUrl}/auth/github/callback'],
                'toutiao' => ['client_id'=>'','client_secret'=>'', 'redirect'=>'{$userUrl}/auth/toutiao/callback'],
                'wework' => ['client_id'=>'','client_secret'=>'', 'redirect'=>'{$userUrl}/auth/wework/callback'],
                'xigua' => ['client_id'=>'','client_secret'=>'', 'redirect'=>'{$userUrl}/auth/xigua/callback'],
            ],'is_system' => '1','is_open' => '0'],
            ['key'=>'filesystem','name' => '附件存储设置','value' => [
                'default' => 'public',
                'ext_yes' => [],
                'ext_no' => [],
                'max_size' => 1024 * 1024 * 10, //单个文件大小10M
                'storage' => [
                    'public' => [
                        'driver' => \Shopwwi\WebmanFilesystem\Adapter\LocalAdapterFactory::class,
                        'root' => public_path().'/static',
                        'url' => '//127.0.0.1:8787/static' // 静态文件访问域名
                    ],
                    'local' => [
                        'driver' => \Shopwwi\WebmanFilesystem\Adapter\LocalAdapterFactory::class,
                        'root' => runtime_path(),
                        'url' => '//127.0.0.1:8787' // 静态文件访问域名
                    ],
                    'ftp' => [
                        'driver' => \Shopwwi\WebmanFilesystem\Adapter\FtpAdapterFactory::class,
                        'host' => 'ftp.example.com',
                        'username' => 'username',
                        'password' => 'password',
                        'url' => '' // 静态文件访问域名
                        // 'port' => 21,
                        // 'root' => '/path/to/root',
                        // 'passive' => true,
                        // 'ssl' => true,
                        // 'timeout' => 30,
                        // 'ignorePassiveAddress' => false,
                        // 'timestampsOnUnixListingsEnabled' => true,
                    ],
                    'memory' => [
                        'driver' => \Shopwwi\WebmanFilesystem\Adapter\MemoryAdapterFactory::class,
                    ],
                    's3' => [
                        'driver' => \Shopwwi\WebmanFilesystem\Adapter\S3AdapterFactory::class,
                        'credentials' => [
                            'key' => 'S3_KEY',
                            'secret' => 'S3_SECRET',
                        ],
                        'region' => 'S3_REGION',
                        'version' => 'latest',
                        'bucket_endpoint' => false,
                        'use_path_style_endpoint' => false,
                        'endpoint' => 'S3_ENDPOINT',
                        'bucket_name' => 'S3_BUCKET',
                        'url' => '' // 静态文件访问域名
                    ],
                    'minio' => [
                        'driver' => \Shopwwi\WebmanFilesystem\Adapter\S3AdapterFactory::class,
                        'credentials' => [
                            'key' => 'S3_KEY',
                            'secret' => 'S3_SECRET',
                        ],
                        'region' => 'S3_REGION',
                        'version' => 'latest',
                        'bucket_endpoint' => false,
                        'use_path_style_endpoint' => true,
                        'endpoint' => 'S3_ENDPOINT',
                        'bucket_name' => 'S3_BUCKET',
                        'url' => '' // 静态文件访问域名
                    ],
                    'oss' => [
                        'driver' => \Shopwwi\WebmanFilesystem\Adapter\AliyunOssAdapterFactory::class,
                        'accessId' => 'OSS_ACCESS_ID',
                        'accessSecret' => 'OSS_ACCESS_SECRET',
                        'bucket' => 'OSS_BUCKET',
                        'endpoint' => 'OSS_ENDPOINT',
                        'url' => '' // 静态文件访问域名
                        // 'timeout' => 3600,
                        // 'connectTimeout' => 10,
                        // 'isCName' => false,
                        // 'token' => null,
                        // 'proxy' => null,
                    ],
                    'qiniu' => [
                        'driver' => \Shopwwi\WebmanFilesystem\Adapter\QiniuAdapterFactory::class,
                        'accessKey' => 'QINIU_ACCESS_KEY',
                        'secretKey' => 'QINIU_SECRET_KEY',
                        'bucket' => 'QINIU_BUCKET',
                        'domain' => 'QINBIU_DOMAIN',
                        'url' => '' // 静态文件访问域名
                    ],
                    'cos' => [
                        'driver' => \Shopwwi\WebmanFilesystem\Adapter\CosAdapterFactory::class,
                        'region' => 'COS_REGION',
                        'app_id' => 'COS_APPID',
                        'secret_id' => 'COS_SECRET_ID',
                        'secret_key' => 'COS_SECRET_KEY',
                        // 可选，如果 bucket 为私有访问请打开此项
                        // 'signed_url' => false,
                        'bucket' => 'COS_BUCKET',
                        'read_from_cdn' => false,
                        'url' => '' // 静态文件访问域名
                        // 'timeout' => 60,
                        // 'connect_timeout' => 60,
                        // 'cdn' => '',
                        // 'scheme' => 'https',
                    ],
                ]
            ],'is_system' => '1','is_open' => '0'],
            ['key'=>'userLoginImages','name' => '地图服务设置','value' => [
               ['image'=>'/static/uploads/user_login_bg.png','bgColor'=>'','imageName'=>'uploads/user_login_bg.png']
            ],'is_system' => '1','is_open' => '0'],
        ];
    }
}