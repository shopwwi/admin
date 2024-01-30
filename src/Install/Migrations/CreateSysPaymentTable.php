<?php

namespace Shopwwi\Admin\Install\Migrations;

use Illuminate\Database\Schema\Blueprint;
use support\Db;

class CreateSysPaymentTable
{

    /**
     * 写入数据
     * @return void
     */
    public static function up()
    {
        Db::connection()->getSchemaBuilder()->create('sys_payment', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable()->comment('支付名称');
            $table->string('code')->nullable()->comment('支付编号');
            $table->text('config')->nullable()->comment('支付设置');
            $table->char('wap', 1)->nullable()->default('0')->comment('h5');
            $table->char('app', 1)->nullable()->default('0')->comment('app');
            $table->char('web', 1)->nullable()->default('0')->comment('web');
            $table->char('status', 1)->nullable()->default('0')->comment('支付状态');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->comment('系统支付方式表');
        });
    }

    public static function down()
    {
        Db::connection()->getSchemaBuilder()->dropIfExists('sys_payment');
    }
}