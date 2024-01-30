<?php

namespace Shopwwi\Admin\Install\Migrations;

use Illuminate\Database\Schema\Blueprint;
use support\Db;

class CreateUserBalanceLogTable
{

    /**
     * 写入数据
     * @return void
     */
    public static function up()
    {
        Db::connection()->getSchemaBuilder()->create('user_balance_log', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('#');
            $table->integer('sys_user_id')->nullable()->comment('管理员ID');
            $table->string('sys_user_name')->nullable()->comment('管理员名称');
            $table->decimal('old_amount', 20)->nullable()->comment('原来余额');
            $table->decimal('available_balance', 20)->nullable()->comment('可用余额');
            $table->decimal('frozen_balance', 20)->nullable()->comment('冻结余额');
            $table->bigInteger('user_id')->comment('会员ID');
            $table->string('user_name')->nullable()->comment('会员名称');
            $table->string('operation_stage', 50)->comment('变动类型');
            $table->string('description')->nullable()->comment('变动说明');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->comment('用户金额变动日志');
        });
    }

    public static function down()
    {
        Db::connection()->getSchemaBuilder()->dropIfExists('user_balance_log');
    }
}