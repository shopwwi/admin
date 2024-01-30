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


namespace Shopwwi\Admin\Libraries;


class StatusCode
{
    const
        STATUS_ZERO = 0, //常用0
        STATUS_YES = 1, //常用1
        STATUS_NO  = 0, //常用2
        NUMBER_ONE = 1, //数字1
        NUMBER_TWO = 2, //数字2
        NUMBER_THREE = 3, //数字3
        NUMBER_FORE = 4, //数字4
        NUMBER_FIVE = 5, //数字5
        NUMBER_SIX = 6, //数字6
        NUMBER_SEVEN = 7, //数字7
        NUMBER_EIGHT = 8, //数字8
        NUMBER_NINE = 9, //数字9

        STATUS_OPEN = 1, //开启状态,审核通过
        STATUS_CLOSE = 2, //关闭状态,审核失败
        STATUS_LOCK = 0 , //锁定状态,待审核

        STATUS_ONLINE = 'ONLINE', //出售中
        STATUS_OFFLINE = 'OFFLINE', //仓库中
        STATUS_BAN = 'BAN',  //违规禁售

        VERIFY_WAIT = 0 , //等待审核
        VERIFY_PASS = 1,//审核通过
        VERIFY_FAIL = 2 , //审核失败
        VERIFY_UN = 8, // 解除审核或关闭

        MODAL_RETAIL = 'RETAIL', //零售形式
        MODAL_WHOLESALE = 'WHOLESALE', //批发形式
        MODAL_VIRTUAL = 'VIRTUAL', //虚拟形式
        MODAL_FOREIGN = 'FOREIGN', //海外形式

        PROMOTION_GENERAL = 0, //无
        PROMOTION_DISCOUNT = 1, //限时折扣
        PROMOTION_PRESELL = 2, //全款预售
        PROMOTION_BOOK = 3, //定金预售
        PROMOTION_GROUP = 4,    //多人拼团
        PROMOTION_BUNDLING = 5, //优惠套装
        PROMOTION_SECKILL = 6,  //秒杀活动
        PROMOTION_BARGAIN = 7,  //砍价活动

        STATUS_INTEL_OK = 200,
        STATUS_INTEL_FAIL = 203,

        PAGE_LIMIT = 15,
        //订单状态
        ORDERS_NEW = 10, //未付款
        ORDERS_CANCEL = 2, //取消
        ORDERS_PAY = 20, //已支付 待发货
        ORDERS_SEND = 30, //已发货
        ORDERS_FINISH = 40, //已完成
        ORDERS_EVALUATE = 3, // 待评价 不写入数据库

        COUPON_USE_ALL = 'ALL', // 全品类
        COUPON_USE_CATEGORY = 'GOODS', //部分商品分类
        COUPON_USE_GOODS = 'CATEGORY', //部分商品

        COUPON_TYPE_FREE = 'FREE', //免费
        COUPON_TYPE_PWD = 'PWD', // 卡密
        COUPON_TYPE_POINTS = 'POINT', //积分
        COUPON_TYPE_ACTIVITY = 'ACTIVITY', //活动赠送
        COUPON_TYPE_GIFTS = 'GIFT', //礼包领取


        COUPON_STATUS_USED = 'USED', //已使用
        COUPON_STATUS_NEW = 'UNUSED', //未使用
        COUPON_STATUS_END = 'EXPIRE', //已过期
        COUPON_STATUS_OUT = 'REPEAL', //已作废

        //ORDERS_PAY支付订单
        ORDERS_TYPE_NORMAL = 1, //普通订单支付
        ORDERS_TYPE_BOOK_DOWN = 2, //预定订单支付定金
        ORDERS_TYPE_BOOK_FINAL = 3, //预定订单支付尾款
        ORDERS_TYPE_BOOK_FULL = 4, // 预定订单全款支付
        ORDERS_TYPE_CHAIN = 5, // 门店订单支付
        ORDERS_TYPE_VIRTUAL = 6, // 虚拟订单支付

        //ORDERS 订单使用
        TYPE_ORDERS_NORMAL = 1, //普通订单
        TYPE_ORDERS_BOOK = 2, //定金预售
        TYPE_ORDERS_GROUPS = 3, //多人拼团
        TYPE_ORDERS_CHAIN = 4, //门店
        TYPE_ORDERS_VIRTUAL = 5, //虚拟
        TYPE_ORDERS_FOREIGN = 6, //海外购
        TYPE_ORDERS_BARGAIN = 7, //砍价
        TYPE_ORDERS_NAME = [1=>'普通订单',2=>'定金预售',3=>'多人拼团',4=>'门店',5=>'虚拟',6=>'海外购',7=>'砍价'],

        PAYMENT_CODE_BALANCE = 'balance', //预存款
        PAYMENT_CODE_ALIPAY = 'alipay', //支付宝
        PAYMENT_CODE_OFFLINE = 'offline', //货到付款
        PAYMENT_CODE_CHAIN = 'chain', //门店支付
        PAYMENT_CODE_WXPAY = 'wxpay', //微信支付
        PAYMENT_CODE_TRANSFER = 'transfer', //对公转账
        PAYMENT_ONLINE = 'online',
        PAYMENT_OFFLINE = 'offline',

        //通用活动状态
        PROMOTION_STATUS_READY = 'READY', //即将开始
        PROMOTION_STATUS_PROCESSING = 'PROCESSING', //已经开始，正在进行
        PROMOTION_STATUS_EXPIRED = 'EXPIRED', //已经结束
        PROMOTION_STATUS_VERIFY_WAIT = 'WAIT', // 等待审核
        PROMOTION_STATUS_VERIFY_FAIL = 'FAIL', // 审核失败
        PROMOTION_STATUS_CLOSE = 'CLOSE', //已关闭终止

        BILL_ORDERS_NEW =  10, //新订单
        BILL_ORDERS_CANCEL = 2, //订单已取消
        BILL_ORDERS_CREATE = 20, //订单进行中
        BILL_ORDERS_OLD_FINISH = 30, //订单完成
        BILL_ORDERS_REFUND = 40, //订单退换货中
        BILL_ORDERS_FINISH = 50, //订单结算完成


        CACHE_EXPIRE = 60, //缓存过期时间（分钟）
        CART_DB_MAX_COUNT = 100, //线上购物车商品存入数据库时最大存储数量
        CHAIN_CART_DB_MAX_COUNT = 60, //门店购物车商品存入数据库时最大存储数量(登录后)
        CHAIN_CART_COOKIE_MAX_COUNT = 5, //门店购物车商品存入数据库时最大存储数量(登录前)
        SMS_AUTHCODE_RESEND_TIME = 60, //发送动态码的重发间隔秒数
        SMS_AUTHCODE_VALID_TIME = 600, //动态码的有效秒数
        SMS_AUTHCODE_SAMEPHONE_MAXNUM = 20,  //同一手机号24小时内发送动态码最大次数
        SMS_AUTHCODE_SAMEIP_MAXNUM = 20,  //同一IP24小时内，发送动态码最大次数
        EMAIL_AUTHCODE_RESEND_TIME = 20,  //邮件发送动态码的重发间隔秒数
        EMAIL_AUTHCODE_VALID_TIME = 1800, //邮件动态码的有效秒数
        EMAIL_AUTHCODE_SAMEPHONE_MAXNUM = 20, //邮件同一手机号24小时内发送动态码最大次数
        EMAIL_AUTHCODE_SAMEIP_MAXNUM = 20, // 邮件同一IP24小时内，发送动态码最大次数
        ORDER_AUTO_CANCEL_TIME = 24, //订单超过N小时未支付自动取消
        ADDRESS_MAX_NUM = 20, //会员最多添加收货地址数量
        INVOICE_MAX_NUM = 20, //会员最多添加发票数量
        ORDER_AUTO_RECEIVE_TIME = 15, //订单发货后超过N天未收货自动收货
        SPEC_MAX_NUM = 3, // 最大规格数
        SPEC_VALUE_MAX_NUM = 10, //最大规格值数
        ORDER_EVALUATION_MAX_TIME = 15, // 订单完成后多少天内可以评价
        ORDER_EVALUATION_APPEND_MAX_TIME = 3, //订单完成后3个月内可以追加评价
        PROMOTION_COUNT_DOWN = 2, //促销倒计时在距当前时间几天开始显示
        DISCOUNT_COUNT_MAX_NUM = 10, //未开始及进行中的最大店铺限时折扣数量
        CONFORM_COUNT_MAX_NUM = 10, //未开始及进行中的最大店铺满优惠数量
        BOOK_AUTO_END_TIME = 72, //预订尾款支付期限(小时)
        GIFT_ONE_COUNT_MAX_NUM = 5, //单件商品添加赠品最大数量
        GROUP_COUNT_MAX_NUM = 10, //未开始及进行中的最大店铺多人拼团数量
        ORDER_COMPLAIN_MAX_TIME = 30, //订单完成后多少天内可以投诉
        ARRIVAL_NOTICE_MAX_TIME = 30, //到货通知最大保存时间（天）
        STORE_RENEW_NOTICE_TIME = 30, //距离店铺到期小于多少天时出现店铺提醒
        DEFAULT_DISTRIBUTION_COMMISSION_RATE = 5, //分销商品默认分佣比例(1~30之间)
        CART_COOKIE_MAX_COUNT = 5, //购物车商品存入Cookie时最大存储数量
        DISTRIBUTION_ORDERS_AUTO_PAST_DUE = 24, //推广单自动过去时间（小时）
        ORDER_REFUND = 15, //收货完成后可以申请退款退货（天）
        REFUND_CONFIRM = 7, //卖家不处理退款退货申请时按同意处理（天）
        RETURN_AUTO_CANCEL = 3, //退货的商品多少天不发货自动取消退货申请
        RETURN_AUTO_RECEIVE_REMIND = 7,//退货的商品 买家发货多少天后卖家不确认收货，发送收货提醒
        RETURN_AUTO_RECEIVE = 10, //退货的商品 买家发货多少天后卖家不确认收货，卖家自动确认收货
        TRYS_BUY_END_TIME = 15, //获得试用资格后多少天内必须使用该资格下单
        TRYS_REPORT_END_TIME = 90, //收货后多少天内必须提交试用报告
        COMPLAIN_MAX_NUM = 3, //订单商品可以最大投诉次数
        EXPORT_COUNT = 500, // 每批导出数量(订单、结算、退单)
        EXPORT_SMALL_COUNT = 200, //每批导出数量(拼团)
        COMPLAIN_MAX_ACCUSED_TIME = 7, //管理员审核后，商家多少天未申诉，系统自动更改申诉状态，进入对话环节
        VIRTUAL_CODE_USE_TIME = 15, //虚拟码使用期限（天）
        VIRTUAL_CODE_SEND_TIMES = 5, // 每种商品虚拟码最多发次数
        VIRTUAL_CODE_REFUND_TIME = 15, //虚拟码过期后多少天内可以申请退款
        ORDERS_DELAY_RECEIVE_SHOW = 2, //离自动收货不足2天时还未收到货可进行延迟收货操作
        MAX_REAL_STORE = 20 , // 商家实体店最大数量

        FOREIGN_BUY_AMOUNT = 20000, // 海外购商品最大购买金额(0为不限制)
        SECKILL_SCHEDULE_CONTINUE_TIME = 24, // 秒杀每期活动持续时间(小时)
        SECKILL_HOME_SHOW_TIME = 24, //前台显示即将开始活动时间（小时）
        SECKILL_ADVANCE_CREATE_TIME = 5, //秒杀预先创建几天的秒杀排序（天，包括当天）
        VOUCHERTEMPLATE_COUNT_MAX_NUM = 10, //每个店铺进行中的店铺券活动最大数量
        COUPON_RECEIVE_MAX_NUM = 100, //会员可领取的平台券最大数量（仅限制免费领取类型平台券，其他类型平台券不限制）
        VOUCHER_RECEIVE_MAX_NUM = 100, //会员可领取的店铺券最大数量（仅限制免费领取类型店铺券，其他类型店铺券不限制）
        CANCEL_REASON_USER = [1=>'我不想买了',2=>'信息填写错误',3 => '卖家缺货' ,4 =>'付款遇到问题' ,5 =>'买错了' ,6 =>'其它原因'],
        CANCEL_REASON_SYSTEM = [ 30 => '超时未支付订单', 31 => '超期未支付全款', 32=>'超期未支付定金' ,33 =>'超期未支付尾款', 34 => '拼团失败退款' ],
        CANCEL_REASON_ADMIN = [ 40 => '全部退款', 41 => '其它原因' ,42 => '全部退款[定金除外]'],
        SHIP_TIME_TYPE = [['value'=> 0, 'label'=> '工作日、双休日与节假日均可送货' ],['value'=> 1,'label'=>'仅双休日送货'],['value'=> 2,'label'=>'仅工作日送货']],

        CHAIN_ORDERS_TYPE_ONLINE = 100,
        CHAIN_ORDERS_TYPE_OFFLINE = 101

    ;

    /**
     * 由细分客户端类型获取主客户端类型
     * @param $clientType
     * @return string
     */
    public static function getPromotionClientType($clientType)
    {
        $str = 'api';
        switch ($clientType) {
            case 'android':
            case 'ios':
            case 'wap':
                $str = 'app';
                break;
            case 'card':
            case 'miniProgram':
                $str = 'card';
                break;
            case 'api':
                $str = 'api';
                break;
        }
        return $str;
    }

    public static function getShipTimeType($value)
    {
        foreach (self::SHIP_TIME_TYPE as $item){
            if($item['value'] = $value){
                return $item['label'];
            }
        }
        return self::SHIP_TIME_TYPE[0]['label'];
    }

    /**
     * 获取订单商品类型
     * @param $type
     * @return string
     */
    public static function getOrdersGoodsTypeText($type)
    {
        switch($type) {
            case self::PROMOTION_DISCOUNT:
                return '限时折扣';
            case self::PROMOTION_PRESELL:
                return '全款预售';
            case self::PROMOTION_BOOK:
                return '定金预售';
            case self::PROMOTION_GROUP:
                return '多人拼团';
            case self::PROMOTION_BUNDLING:
                return '优惠套装';
            case 50:
                return '门店';
            case 51:
                return '虚拟';
            case self::PROMOTION_SECKILL:
                return '秒杀';
            case 52:
                return '海外购';
            case self::PROMOTION_BARGAIN:
                return '砍价';
            case 101:
                return '付邮试用';
            case 102:
                return '返券试用';
            default:
                return "";
        }
    }
}
