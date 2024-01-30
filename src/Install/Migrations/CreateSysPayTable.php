<?php

namespace Shopwwi\Admin\Install\Migrations;

use Illuminate\Database\Schema\Blueprint;
use support\Db;

class CreateSysPayTable
{

    /**
     * 写入数据
     * @return void
     */
    public static function up()
    {
        Db::connection()->getSchemaBuilder()->create('sys_pay', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('pay_sn')->nullable()->comment('支付单号');
            $table->decimal('amount', 20)->nullable()->comment('支付金额');
            $table->decimal('refund', 20)->nullable()->default(0)->comment('退款金额');
            $table->string('payment_name')->nullable()->comment('支付名称');
            $table->string('payment_code')->nullable()->comment('支付编码');
            $table->char('status', 1)->nullable()->default('0')->comment('支付状态');
            $table->timestamp('pay_time')->nullable()->comment('支付时间');
            $table->string('pay_type')->nullable()->comment('支付类型');
            $table->string('pay_client_type')->nullable()->comment('支付终端');
            $table->integer('pay_type_id')->nullable()->comment('对应支付类型');
            $table->text('pay_return')->nullable()->comment('支付状态返回触发');
            $table->string('out_sn')->nullable()->comment('外部交易号');
            $table->integer('user_id')->nullable()->comment('会员编号');
            $table->string('reason')->nullable()->comment('变更理由');
            $table->integer('updated_user_id')->nullable()->comment('变更人员');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->comment('系统支付表');
        });
    }

    public static function down()
    {
        Db::connection()->getSchemaBuilder()->dropIfExists('sys_pay');
    }
}