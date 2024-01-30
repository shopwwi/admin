<?php
/**
 *-------------------------------------------------------------------------s*
 * 支付处理
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



use Carbon\Carbon;
use Shopwwi\Admin\App\Admin\Models\SysPay;
use Shopwwi\Admin\App\Admin\Models\SysPayment;
use Shopwwi\LaravelCache\Cache;
use Yansongda\Pay\Pay;

class PayLogic
{
    public static function config()
    {
        return [
            'alipay' => [
                'default' => [
                    // 必填-支付宝分配的 app_id
                    'app_id' => '2016082000295641',
                    // 必填-应用私钥 字符串或路径
                    'app_secret_cert' => 'MIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQCDRjOg5DnX+8L+rB8d2MbrQ30Z7JPM4hiDhawHSwQCQ7RlmQNpl6b/N6IrPLcPFC1uii179U5Il5xTZynfjkUyJjnHusqnmHskftLJDKkmGbSUFMAlOv+NlpUWMJ2A+VUopl+9FLyqcV+XgbaWizxU3LsTtt64v89iZ2iC16H6/6a3YcP+hDZUjiNGQx9cuwi9eJyykvcwhDkFPxeBxHbfwppsul+DYUyTCcl0Ltbga/mUechk5BksW6yPPwprYHQBXyM16Jc3q5HbNxh3660FyvUBFLuVWIBs6RtR2gZCa6b8rOtCkPQKhUKvzRMlgheOowXsWdk99GjxGQDK5W4XAgMBAAECggEAYPKnjlr+nRPBnnNfR5ugzH67FToyrU0M7ZT6xygPfdyijaXDb2ggXLupeGUOjIRKSSijDrjLZ7EQMkguFHvtfmvcoDTDFaL2zq0a3oALK6gwRGxOuzAnK1naINkmeOmqiqrUab+21emEv098mRGbLNEXGCgltCtz7SiRdo/pgIPZ1wHj4MH0b0K2bFG3xwr51EyaLXKYH4j6w9YAXXsTdvzcJ+eRE0Yq4uGPfkziqg8d0xXSEt90HmCGHKo4O2eh1w1IlBcHfK0F6vkeUAtrtAV01MU2bNoRU147vKFxjDOVBlY1nIZY/drsbiPMuAfSsodL0hJxGSYivbKTX4CWgQKBgQDd0MkF5AIPPdFC+fhWdNclePRw4gUkBwPTIUljMP4o+MhJNrHp0sEy0sr1mzYsOT4J20hsbw/qTnMKGdgy784bySf6/CC7lv2hHp0wyS3Es0DRJuN+aTyyONOKGvQqd8gvuQtuYJy+hkIoHygjvC3TKndX1v66f9vCr/7TS0QPywKBgQCXgVHERHP+CarSAEDG6bzI878/5yqyJVlUeVMG5OXdlwCl0GAAl4mDvfqweUawSVFE7qiSqy3Eaok8KHkYcoRlQmAefHg/C8t2PNFfNrANDdDB99f7UhqhXTdBA6DPyW02eKIaBcXjZ7jEXZzA41a/zxZydKgHvz4pUq1BdbU5ZQKBgHyqGCDgaavpQVAUL1df6X8dALzkuqDp9GNXxOgjo+ShFefX/pv8oCqRQBJTflnSfiSKAqU2skosdwlJRzIxhrQlFPxBcaAcl0VTcGL33mo7mIU0Bw2H1d4QhAuNZIbttSvlIyCQ2edWi54DDMswusyAhHxwz88/huJfiad1GLaLAoGASIweMVNuD5lleMWyPw2x3rAJRnpVUZTc37xw6340LBWgs8XCEsZ9jN4t6s9H8CZLiiyWABWEBufU6z+eLPy5NRvBlxeXJOlq9iVNRMCVMMsKybb6b1fzdI2EZdds69LSPyEozjkxdyE1sqH468xwv8xUPV5rD7qd83+pgwzwSJkCgYBrRV0OZmicfVJ7RqbWyneBG03r7ziA0WTcLdRWDnOujQ9orhrkm+EY2evhLEkkF6TOYv4QFBGSHfGJ0SwD7ghbCQC/8oBvNvuQiPWI8B+00LwyxXNrkFOxy7UfIUdUmLoLc1s/VdBHku+JEd0YmEY+p4sjmcRnlu4AlzLxkWUTTg==',
                    // 必填-应用公钥证书 路径
                    'app_public_cert_path' => '/Users/yansongda/pay/cert/appCertPublicKey_2016082000295641.crt',
                    // 必填-支付宝公钥证书 路径
                    'alipay_public_cert_path' => '/Users/yansongda/pay/cert/alipayCertPublicKey_RSA2.crt',
                    // 必填-支付宝根证书 路径
                    'alipay_root_cert_path' => '/Users/yansongda/pay/cert/alipayRootCert.crt',
                    'return_url' => 'https://yansongda.cn/alipay/return',
                    'notify_url' => 'https://yansongda.cn/alipay/notify',
                    // 选填-第三方应用授权token
                    'app_auth_token' => '',
                    // 选填-服务商模式下的服务商 id，当 mode 为 Pay::MODE_SERVICE 时使用该参数
                    'service_provider_id' => '',
                    // 选填-默认为正常模式。可选为： MODE_NORMAL, MODE_SANDBOX, MODE_SERVICE
                    'mode' => Pay::MODE_NORMAL,
                ]
            ],
            'wechat' => [
                'default' => [
                    // 必填-商户号，服务商模式下为服务商商户号
                    'mch_id' => '',
                    // 必填-商户秘钥
                    'mch_secret_key' => '',
                    // 必填-商户私钥 字符串或路径
                    'mch_secret_cert' => '',
                    // 必填-商户公钥证书路径
                    'mch_public_cert_path' => '',
                    // 必填
                    'notify_url' => 'https://yansongda.cn/wechat/notify',
                    // 选填-公众号 的 app_id
                    'mp_app_id' => '2016082000291234',
                    // 选填-小程序 的 app_id
                    'mini_app_id' => '',
                    // 选填-app 的 app_id
                    'app_id' => '',
                    // 选填-合单 app_id
                    'combine_app_id' => '',
                    // 选填-合单商户号
                    'combine_mch_id' => '',
                    // 选填-服务商模式下，子公众号 的 app_id
                    'sub_mp_app_id' => '',
                    // 选填-服务商模式下，子 app 的 app_id
                    'sub_app_id' => '',
                    // 选填-服务商模式下，子小程序 的 app_id
                    'sub_mini_app_id' => '',
                    // 选填-服务商模式下，子商户id
                    'sub_mch_id' => '',
                    // 选填-微信公钥证书路径, optional，强烈建议 php-fpm 模式下配置此参数
                    'wechat_public_cert_path' => [
                        '45F59D4DABF31918AFCEC556D5D2C6E376675D57' => __DIR__.'/Cert/wechatPublicKey.crt',
                    ],
                    // 选填-默认为正常模式。可选为： MODE_NORMAL, MODE_SERVICE
                    'mode' => Pay::MODE_NORMAL,
                ]
            ],
            'unipay' => [
                'default' => [
                    // 必填-商户号
                    'mch_id' => '777290058167151',
                    // 必填-商户公私钥
                    'mch_cert_path' => __DIR__.'/Cert/unipayAppCert.pfx',
                    // 必填-商户公私钥密码
                    'mch_cert_password' => '000000',
                    // 必填-银联公钥证书路径
                    'unipay_public_cert_path' => __DIR__.'/Cert/unipayCertPublicKey.cer',
                    // 必填
                    'return_url' => 'https://yansongda.cn/unipay/return',
                    // 必填
                    'notify_url' => 'https://yansongda.cn/unipay/notify',
                ],
            ],
            'logger' => [
                'enable' => false,
                'file' => './logs/pay.log',
                'level' => 'info', // 建议生产环境等级调整为 info，开发环境为 debug
                'type' => 'single', // optional, 可选 daily.
                'max_file' => 30, // optional, 当 type 为 daily 时有效，默认 30 天
            ],
            'http' => [ // optional
                'timeout' => 5.0,
                'connect_timeout' => 5.0,
                // 更多配置项请参考 [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
            ],
        ];
    }

    /**
     * 管理员编辑支付状态后续处理
     * @param $payInfo
     * @return mixed
     */
    public static function adminPay($payInfo)
    {
        $handle = new $payInfo->pay_return;
        return $handle->handle($payInfo);
    }

    /**
     * 支付回调处理
     */
    public static function return($result,$type)
    {
        $payInfo =  SysPay::where('pay_sn',$result->paySn)->first();
        if($payInfo == null){
            throw new \Exception('支付单不存在');
        }
        $payInfo->status = 1;
        $payInfo->pay_time = Carbon::now();
        $payInfo->payment_code = $type;
        $payInfo->out_sn = $result['out_sn'];
        $payInfo->save();
        self::adminPay($payInfo);
    }

    public static function getAlipayStatus($paySn){
        Pay::config(self::config());
        $order = [
            'out_trade_no' => $paySn,
        ];
        $result = Pay::alipay()->find($order);
    }
    public static function getWxpayStatus($paySn){
        Pay::config(self::config());
        $order = [
            'out_trade_no' => $paySn,
        ];
        $result = Pay::wechat()->find($order);
    }
    public static function getUnipayStatus($paySn){
        Pay::config(self::config());
        $order = [
            'txnTime' => '20220911041647',
            'orderId' => $paySn,
        ];
        $result = Pay::unipay()->find($order);
    }

    /**
     * 获取支付列表
     * @return mixed
     */
    public static function getPaymentList(){
        $list = Cache::rememberForever('shopwwiSysPayment', function () {
            return SysPayment::where('status',1)->orderBy('id','asc')->get();
        });
        return $list;
    }
    /**
     * 清理支付列表缓存
     * @return void
     */
    public static function clear()
    {
        Cache::forget("shopwwiSysPayment");
    }

    /**
     * 判断货到付款是否开启
     * @return bool
     */
    public static function isOfflinePaymentOpen(){
        $list = self::getPaymentList();
        $has = $list->where('code','offline')->first();
        if ($has ==null){
            return false;
        }else{
            return $has->status == 1;
        }
    }
}