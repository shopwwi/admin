<?php

namespace Shopwwi\Admin\Install\Seeders;

use Shopwwi\Admin\App\Admin\Models\SysPayment;

class SysPaymentSeeder
{
    public static function run()
    {
        $list = self::getData();
        foreach ($list as $item){
            SysPayment::updateOrCreate(['code'=>$item['code']],$item);
        }
    }
    protected static function getData()
    {
        return [
            ['code' => 'alipay', 'name' => '支付宝', 'wap' => 1, 'web' => 1, 'app' => 1,'mini' => 1,'status' => 0,'config'=>[
                'app_id'=>'',
                'app_secret_cert'=>'',
                'app_public_cert_path'=>'pay/alipay/appCertPublicKey.crt',
                'alipay_public_cert_path'=>'pay/alipay/alipayCertPublicKey_RSA2.crt',
                'alipay_root_cert_path'=>'pay/alipay/alipayRootCert.crt',
                'return_url'=>'{$userUrl}/pay/alipay/return',
                'notify_url'=>'{$userUrl}/pay/alipay/notify',
                'app_auth_token' => '',
                'service_provider_id' => '',
                'mode' => \Yansongda\Pay\Pay::MODE_NORMAL,
            ]],
            ['code' => 'wxpay', 'name' => '微信支付', 'wap' => 1, 'web' => 1, 'app' => 1,'mini' => 1,'status' => 0,'config'=>[
                'mch_id'=>'',
                'mch_secret_key_v2'=>'',
                'mch_secret_key'=>'',
                'mch_secret_cert'=>'pay/wxpay/apiclient_key.pem',
                'mch_public_cert_path'=>'pay/wxpay/apiclient_cert.pem',
                'notify_url'=>'{$userUrl}/pay/wxpay/notify',
                'mp_app_id' => '',
                'mini_app_id' => '',
                'app_id' => '',
                'combine_app_id' => '',
                'combine_mch_id' => '',
                'sub_mp_app_id' => '',
                'sub_app_id' => '',
                'sub_mini_app_id' => '',
                'sub_mch_id' => '',
                'wechat_public_cert_path' => [],
                'mode' => \Yansongda\Pay\Pay::MODE_NORMAL,
            ]],
            ['code' => 'unipay', 'name' => '银联支付', 'wap' => 1, 'web' => 1, 'app' => 1,'mini' => 1,'status' => 0,'config'=>[
                'mch_id'=>'',
                'mch_cert_path'=>'pay/unipay/unipayAppCert.pfx',
                'mch_cert_password'=>'',
                'unipay_public_cert_path'=>'pay/unipay/unipayCertPublicKey.cer',
                'return_url'=>'{$userUrl}/pay/unipay/return',
                'notify_url'=>'{$userUrl}/pay/unipay/notify',
            ]],
            ['code' => 'balance', 'name' => '预存款', 'wap' => 1, 'web' => 1, 'app' => 1,'mini' => 1,'status' => 0],
            ['code' => 'offline', 'name' => '货到付款', 'wap' => 1, 'web' => 1, 'app' => 1,'mini' => 1,'status' => 0],
        ];
    }
}