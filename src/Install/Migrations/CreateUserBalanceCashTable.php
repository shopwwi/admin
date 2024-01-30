<?php

namespace Shopwwi\Admin\Install\Migrations;

use Illuminate\Database\Schema\Blueprint;
use support\Db;

class CreateUserBalanceCashTable
{

    /**
     * 写入数据
     * @return void
     */
    public static function up()
    {
        Db::connection()->getSchemaBuilder()->create('user_balance_cash', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('sys_user_id')->nullable()->comment('管理员ID');
            $table->string('sys_user_name')->nullable()->comment('管理员名称');
            $table->string('cash_sn', 100)->comment('提现序列号');
            $table->decimal('cash_amount', 20)->comment('提现金额');
            $table->decimal('service_amount', 20)->comment('提现手续费');
            $table->decimal('amount', 20)->comment('实际到账');
            $table->integer('user_id')->comment('会员ID');
            $table->timestamp('pay_time')->nullable()->comment('支付时间');
            $table->string('refuse_reason', 1000)->nullable()->comment('拒绝提现理由');
            $table->char('cash_status', 1)->default('0')->comment('状态: 0未处理 1提现成功 2提现失败 8取消提现');
            $table->string('bank_name')->nullable()->comment('银行名称');
            $table->string('bank_account')->nullable()->comment('银行账号/支付宝账号/微信号');
            $table->string('bank_username')->nullable()->comment('持卡人');
            $table->string('bank_branch')->nullable()->comment('银行支行');
            $table->string('bank_type')->nullable()->comment('类别 bank 银行 alipay 支付宝 wechat 微信零钱');
            $table->string('out_sn')->nullable()->comment('外部交易号');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->softDeletes()->comment('删除时间');
            $table->comment('用户提现表');
        });
    }

    public static function down()
    {
        Db::connection()->getSchemaBuilder()->dropIfExists('user_balance_cash');
    }
}