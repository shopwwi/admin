<?php

namespace Shopwwi\Admin\Install\Seeders;

use Shopwwi\Admin\App\Admin\Models\SysMsgTplCommon;

class SysMsgTplCommonSeeder
{
    public static function run()
    {
        $list = self::getData();
        foreach ($list as $item){
            SysMsgTplCommon::updateOrCreate(['code'=>$item['code']],$item);
        }
    }

    protected static function getData(){
        return [
            [ 'code'=> 'userBalanceCashFail', 'email_status' => 1, 'sms_status' => 1, 'type' => '1',
                'email_title' => '预存款提现失败 - {$siteName}',
                'email_content' => '<p><span style="font-family: &#39;sans serif&#39;, tahoma, verdana, helvetica; line-height: 18px;">{$siteName}</span><span style="font-family: &#39;sans serif&#39;, tahoma, verdana, helvetica; line-height: 18px;">提现申请</span><span style="font-family: &#39;sans serif&#39;, tahoma, verdana, helvetica; line-height: 18px;">：</span><br/></p><p style="white-space: normal; word-wrap: break-word; font-family: &#39;sans serif&#39;, tahoma, verdana, helvetica; line-height: 18px;">您的提现单号{$cashSn}的提现申请已被管理员拒绝，请登录用户中心账户余额查看。</p><p><br/></p>',
                'notice_content' => '您的提现申请：提现单号{$cashSn}的提现申请已被管理员拒绝，请进入用户中心账户余额查看。',
                'sms_content' => '【{$siteName}】提现申请提示：提现单号{$cashSn}的提现申请已被管理员拒绝，请登录用户中心账户余额查看。',
                'class' => '1004',
                'name' => '预存款提现失败',
                'wechat_mp_template_store_id'=> 'TM00981',
                'wechat_mp_template_store_title' => '提现失败通知'
            ],
            [ 'code'=> 'userBalanceChange', 'email_status' => 1, 'sms_status' => 1, 'type' => '1',
                'email_title' => '预存款变更提现 - {$siteName}',
                'email_content' => '<p><span style="font-family: &#39;sans serif&#39;, tahoma, verdana, helvetica; line-height: 18px;">{$siteName}</span><span style="font-family: &#39;sans serif&#39;, tahoma, verdana, helvetica; line-height: 18px;">预存款</span><span style="font-family: &#39;sans serif&#39;, tahoma, verdana, helvetica; line-height: 18px;">提示：</span><br/></p><p style="white-space: normal; word-wrap: break-word; font-family: &#39;sans serif&#39;, tahoma, verdana, helvetica; line-height: 18px;">您的预存款发生变更，变更金额{$amount}元，请登录用户中心账户余额查看。</p>',
                'notice_content' => '您的预存款：您的预存款发生变更，变更金额{$amount}元，请进入用户中心账户余额查看。',
                'sms_content' => '【{$siteName}】预存款提示：您的预存款发生变更，变更金额{$amount}元，请登录用户中心账户余额查看。',
                'class' => '1004',
                'name' => '预存款变更提现',
                'wechat_mp_template_store_id'=> 'OPENTM402190178',
                'wechat_mp_template_store_title' => '账户资金变动提醒'
            ],
            [ 'code'=> 'userOrdersCancel', 'email_status' => 1, 'sms_status' => 1, 'type' => '1',
                'email_title' => '订单取消提醒 - {$siteName}',
                'email_content' => '<p><span style="font-family: &#39;sans serif&#39;, tahoma, verdana, helvetica; font-size: 12px; line-height: 18px;">{$siteName}</span><span style="font-family: &#39;sans serif&#39;, tahoma, verdana, helvetica; font-size: 12px; line-height: 18px;">订单</span><span style="font-family: &#39;sans serif&#39;, tahoma, verdana, helvetica; font-size: 12px; line-height: 18px;">提示：</span><br/></p><p style="white-space: normal; word-wrap: break-word; font-family: &#39;sans serif&#39;, tahoma, verdana, helvetica; font-size: 12px; line-height: 18px;">您的订单单号{$ordersSn}已于{$cancelTime}取消，请登录用户中心商品订单查看。</p><p><br/></p>',
                'notice_content' => '您的订单：订单单号{$ordersSn}已于{$cancelTime}取消，请进入用户中心商品订单查看。',
                'sms_content' => '【{$siteName}】订单提示：您订单单号{$ordersSn}已于{$cancelTime}取消，请登录用户中心商品订单查看。',
                'class' => '1001',
                'name' => '订单取消提醒',
                'wechat_mp_template_store_id'=> 'OPENTM406648164',
                'wechat_mp_template_store_title' => '订单取消通知'
            ],
            [ 'code'=> 'userOrdersEvaluateExplain', 'email_status' => 1, 'sms_status' => 1, 'type' => '1',
                'email_title' => '评价解释提醒 - {$siteName}',
                'email_content' => '<p><span style="font-family: &#39;sans serif&#39;, tahoma, verdana, helvetica; font-size: 12px; line-height: 18px;">{$siteName}订单提示：<br/></span></p><p style="white-space: normal; word-wrap: break-word; font-family: &#39;sans serif&#39;, tahoma, verdana, helvetica; font-size: 12px; line-height: 18px;"><span style="font-family: sans-serif; font-size: 12px;">您的评价订单单号{$ordersSn}已被卖家{$sellerName}于{$evalTime}解释，请登录用户中心交易评价/晒单查看。</span><br/></p>',
                'notice_content' => '您的评价：订单单号{$ordersSn}已被卖家{$sellerName}于{$evalTime}解释，请进入用户中心交易评价/晒单查看。',
                'sms_content' => '【{$siteName}】评价提示：订单单号{$ordersSn}已被卖家{$sellerName}于{$evalTime}解释，请登录用户中心交易评价/晒单查看。',
                'class' => '1001',
                'name' => '评价解释提醒',
                'wechat_mp_template_store_id'=> 'OPENTM205102209',
                'wechat_mp_template_store_title' => '订单评价提醒'
            ],
            [ 'code'=> 'userOrdersModifyFreight', 'email_status' => 1, 'sms_status' => 1, 'type' => '1',
                'email_title' => '修改运费提醒 - {$siteName}',
                'email_content' => '<p><span style="font-family: &#39;sans serif&#39;, tahoma, verdana, helvetica; font-size: 12px; line-height: 18px;">{$siteName}</span><span style="font-family: &#39;sans serif&#39;, tahoma, verdana, helvetica; font-size: 12px; line-height: 18px;">订单</span><span style="font-family: &#39;sans serif&#39;, tahoma, verdana, helvetica; font-size: 12px; line-height: 18px;">提示：</span><br/></p><p style="white-space: normal; word-wrap: break-word; font-family: &#39;sans serif&#39;, tahoma, verdana, helvetica; font-size: 12px; line-height: 18px;">您的订单单号{$ordersSn}运费已被商家修改为{$freightAmount}元，请进入用户中心商品订单查看。</p>',
                'notice_content' => '您的订单：订单单号{$ordersSn}运费已被商家修改为{$freightAmount}元，请进入用户中心商品订单查看。',
                'sms_content' => '【{$siteName}】订单提示：您的订单单号{$ordersSn}运费已被商家修改为{$freightAmount}元。',
                'class' => '1001',
                'name' => '修改运费提醒',
                'wechat_mp_template_store_id'=> 'OPENTM407706676',
                'wechat_mp_template_store_title' => '运费修改通知'
            ],
            [ 'code'=> 'userOrdersPay', 'email_status' => 1, 'sms_status' => 1, 'type' => '1',
                'email_title' => '订单支付提醒 - {$siteName}',
                'email_content' => '<p><span style="font-family: &#39;sans serif&#39;, tahoma, verdana, helvetica; font-size: 12px; line-height: 18px;">{$siteName}</span><span style="font-family: &#39;sans serif&#39;, tahoma, verdana, helvetica; font-size: 12px; line-height: 18px;">订单</span><span style="font-family: &#39;sans serif&#39;, tahoma, verdana, helvetica; font-size: 12px; line-height: 18px;">提示：</span></p><p style="white-space: normal; word-wrap: break-word; font-family: &#39;sans serif&#39;, tahoma, verdana, helvetica; font-size: 12px; line-height: 18px;">您的订单单号{$ordersSn}已于{$paymentTime}付款成功，请登录用户中心商品订单查看。</p><p><br/></p>',
                'notice_content' => '您的订单：订单单号{$ordersSn}已于{$paymentTime}付款成功，请进入用户中心商品订单查看。',
                'sms_content' => '【{$siteName}】订单提示：您订单单号{$ordersSn}已于{$paymentTime}付款成功，请登录用户中心商品订单查看。',
                'class' => '1001',
                'name' => '订单支付提醒',
                'wechat_mp_template_store_id'=> 'OPENTM207777974',
                'wechat_mp_template_store_title' => '订单支付成功提示'
            ],
            [ 'code'=> 'userOrdersReceive', 'email_status' => 1, 'sms_status' => 1, 'type' => '1',
                'email_title' => '订单收货提醒 - {$siteName}',
                'email_content' => '',
                'notice_content' => '您的订单：订单单号{$ordersSn}已于{$finishTime}收货完成，请进入用户中心商品订单查看。',
                'sms_content' => '【{$siteName}】订单提示：您的订单单号{$ordersSn}已于{$finishTime}收货完成，请登录用户中心商品订单查看。',
                'class' => '1003',
                'name' => '订单收货提醒',
                'wechat_mp_template_store_id'=> 'OPENTM405976260',
                'wechat_mp_template_store_title' => '收货确认通知'
            ],
            [ 'code'=> 'userOrdersSend', 'email_status' => 1, 'sms_status' => 1, 'type' => '1',
                'email_title' => '订单发货提醒 - {$siteName}',
                'email_content' => '<p style="white-space: normal; word-wrap: break-word; font-family: &#39;sans serif&#39;, tahoma, verdana, helvetica; line-height: 18px;">{$siteName}订单提示：</p><p style="white-space: normal; word-wrap: break-word; font-family: &#39;sans serif&#39;, tahoma, verdana, helvetica; line-height: 18px;"><span style="font-family: sans-serif;">您的订单单号{$ordersSn}已于{$sendTime}发货，运单号：{$shipSn}。请登录用户中心商品订单查看。</span></p>',
                'notice_content' => '您的订单：订单单号{$ordersSn}已于{$sendTime}发货，运单号：{$shipSn}。请进入用户中心商品订单查看。',
                'sms_content' => '【{$siteName}】您的订单：订单单号{$ordersSn}已于{$sendTime}发货，运单号：{$shipSn}。请登录用户中心商品订单查看。',
                'class' => '1003',
                'name' => '订单发货提醒',
                'wechat_mp_template_store_id'=> 'OPENTM200565259',
                'wechat_mp_template_store_title' => '订单发货提醒'
            ],
            [ 'code'=> 'userRefundUpdate', 'email_status' => 1, 'sms_status' => 1, 'type' => '1',
                'email_title' => '退款提醒 - {$siteName}',
                'email_content' => '<p style="word-wrap: break-word; font-family: &#39;sans serif&#39;, tahoma, verdana, helvetica; font-size: 12px; line-height: 18px; white-space: normal;">{$siteName}退款提示：</p><p style="word-wrap: break-word; font-family: &#39;sans serif&#39;, tahoma, verdana, helvetica; font-size: 12px; line-height: 18px; white-space: normal;">您的退单单号{$refundSn}的售后申请已有变化，请登录用户中心售后退款服务查看。</p>',
                'notice_content' => '您的退款：退单单号{$refundSn}的售后申请已有变化，请进入用户中心售后退款服务查看。',
                'sms_content' => '【{$siteName}】退款提示：您的退单单号{$refundSn}的售后申请已有变化，请登录用户中心售后退款服务查看。',
                'class' => '1002',
                'name' => '退款提醒',
                'wechat_mp_template_store_id'=> 'OPENTM405627933',
                'wechat_mp_template_store_title' => '退款提醒'
            ],
            [ 'code'=> 'userReturnAutoCancelNotice', 'email_status' => 1, 'sms_status' => 1, 'type' => '1',
                'email_title' => '退货自动取消提醒 - {$siteName}',
                'email_content' => '<p style="white-space: normal;"><span sans="" font-size:="" line-height:="">{$siteName}</span><span sans="" font-size:="" line-height:="">订单</span><span sans="" font-size:="" line-height:="">提示：</span><br/></p><p word-wrap:="" font-family:="" sans="" font-size:="" line-height:="" style="white-space: normal;">退货单号{$refundSn}超出发货期限自动取消，请登录用户中心退货记录查看。</p>',
                'notice_content' => '您的退货：退货单号{$refundSn}超出发货期限自动取消，请登录用户中心退货记录查看。',
                'sms_content' => '【{$siteName}】提示：退货单号{$refundSn}超出发货期限自动取消，请登录用户中心退货记录查看。',
                'class' => '1002',
                'name' => '退货自动取消提醒',
                'wechat_mp_template_store_id'=> 'OPENTM406292353',
                'wechat_mp_template_store_title' => '退货确认提醒'
            ],
            [ 'code'=> 'userReturnUpdate', 'email_status' => 1, 'sms_status' => 1, 'type' => '1',
                'email_title' => '退货提醒 - {$siteName}',
                'email_content' => '<p style="font-size:12px;text-align:left;">{$siteName}退货提示：</p><p style="font-size:12px;">您的退单单号{$refundSn}的售后申请已有变化，请登录用户中心售后退货服务查看。</p><p><br></p>',
                'notice_content' => '您的退货：退单单号{$refundSn}的售后申请已有变化，请进入用户中心售后退货服务查看。',
                'sms_content' => '【{$siteName}】退货提示：您的退单单号{$refundSn}的售后申请已有变化，请登录用户中心售后退货服务查看。',
                'class' => '1002',
                'name' => '退货提醒',
                'wechat_mp_template_store_id'=> 'OPENTM406292353',
                'wechat_mp_template_store_title' => '退货确认提醒'
            ],
        ];
    }
}