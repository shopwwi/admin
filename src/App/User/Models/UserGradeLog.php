<?php
/**
 *-------------------------------------------------------------------------s*
 * 用户组变更日志模型
 *-------------------------------------------------------------------------h*
 * @copyright  Copyright (c) 2015-2024 Shopwwi Inc. (http://www.shopwwi.com)
 *-------------------------------------------------------------------------o*
 * @license    http://www.shopwwi.com        s h o p w w i . c o m
 *-------------------------------------------------------------------------p*
 * @link       http://www.shopwwi.com by 无锡豚豹科技
 *-------------------------------------------------------------------------w*
 * @since      ShopWWI智能管理系统
 *-------------------------------------------------------------------------w*
 * @author     8988354@qq.com TycoonSong
 *-------------------------------------------------------------------------i*
 * @property integer $id
 * @property integer $price 会员价格
 * @property integer $original_price 原价格
 * @property integer $grade_id 会员等级编号
 * @property integer $status 支付状态 1为已付款 0为未付款
 * @property integer $admin_id 管理员ID
 * @property string $admin_name 管理员名称
 * @property string $pay_time 支付时间
 * @property string $payment_client_type 支付使用终端(WAP,WEB,APP)
 * @property string $payment_code 支付方式标识
 * @property string $payment_name 支付方式名称
 * @property string $log_sn 充值编号
 * @property string $trade_sn 第三方支付接口交易号
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
namespace Shopwwi\Admin\App\User\Models;

class UserGradeLog extends Model
{
    // const CREATED_AT = 'created_at';
    // const UPDATED_AT = 'updated_at';

    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'user_grade_log';

    /**
     * 与表关联的主键
     *
     * @var string
     */
    // protected $primaryKey = 'flight_id';

    /**
     * 主键是否主动递增
     *
     * @var bool
     */
    // public $incrementing = false;

    /**
     * 自动递增主键的「类型」
     *
     * @var string
     */
    // protected $keyType = 'string';

    /**
     * 是否主动维护时间戳
     *
     * @var bool
     */
    // public $timestamps = false;

    /**
     * 模型日期的存储格式
     *
     * @var string
     */
    // protected $dateFormat = 'U';

    /**
     * 模型的数据库连接名
     *
     * @var string
     */
    // protected $connection = 'connection-name';

    /**
     * 可批量赋值属性
     *
     * @var array
     */
    // protected $fillable = [];

    /**
     * 模型属性的默认值
     *
     * @var array
     */
    protected $attributes = [

    ];

    /**
     * 不可批量赋值的属性
     *
     * @var array
     */
    protected $guarded = [];

}
