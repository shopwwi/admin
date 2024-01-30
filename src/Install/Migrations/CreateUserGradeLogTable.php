<?php

namespace Shopwwi\Admin\Install\Migrations;

use Illuminate\Database\Schema\Blueprint;
use support\Db;

class CreateUserGradeLogTable
{

    /**
     * 写入数据
     * @return void
     */
    public static function up()
    {
        Db::connection()->getSchemaBuilder()->create('user_grade_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('price')->nullable()->comment('会员价格');
            $table->bigInteger('original_price')->nullable()->comment('原价格');
            $table->integer('grade_id')->nullable()->comment('会员等级编号');
            $table->char('status', 1)->nullable()->default('0')->comment('支付状态 1为已付款 0为未付款');
            $table->integer('admin_id')->nullable()->comment('管理员ID');
            $table->string('admin_name')->nullable()->comment('管理员名称');
            $table->timestamp('pay_time')->nullable()->comment('支付时间');
            $table->string('payment_client_type')->nullable()->comment('支付使用终端(WAP,WEB,APP)');
            $table->string('payment_code', 50)->nullable()->comment('支付方式标识');
            $table->string('payment_name')->nullable()->comment('支付方式名称');
            $table->string('log_sn', 100)->nullable()->comment('充值编号');
            $table->string('trade_sn', 100)->nullable()->comment('第三方支付接口交易号');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->comment('用户组变更日志');
        });
    }

    public static function down()
    {
        Db::connection()->getSchemaBuilder()->dropIfExists('user_grade_log');
    }
}