<?php

namespace Shopwwi\Admin\Install\Migrations;

use Illuminate\Database\Schema\Blueprint;
use support\Db;

class CreateUserBalanceRechargeTable
{

    /**
     * 写入数据
     * @return void
     */
    public static function up()
    {
        Db::connection()->getSchemaBuilder()->create('user_balance_recharge', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('sys_user_id')->nullable()->comment('管理员ID');
            $table->string('sys_user_name')->nullable()->comment('管理员名称');
            $table->decimal('amount', 20)->nullable()->comment('充值金额');
            $table->decimal('real_amount', 20)->nullable()->comment('实际到账');
            $table->bigInteger('points')->nullable()->comment('赠送积分');
            $table->bigInteger('growth')->nullable()->comment('赠送成长值');
            $table->integer('user_id')->nullable()->comment('会员ID');
            $table->integer('meal_id')->nullable()->comment('套餐编号');
            $table->char('status', 1)->nullable()->default('0')->comment('支付状态（状态 0 未结算 1已结算）');
            $table->string('pay_sn', 100)->nullable()->comment('支付编号');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->softDeletes()->comment('删除时间');
            $table->comment('用户充值表');
        });
    }

    public static function down()
    {
        Db::connection()->getSchemaBuilder()->dropIfExists('user_balance_recharge');
    }
}