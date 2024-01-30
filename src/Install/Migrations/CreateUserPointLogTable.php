<?php

namespace Shopwwi\Admin\Install\Migrations;

use Illuminate\Database\Schema\Blueprint;
use support\Db;

class CreateUserPointLogTable
{

    /**
     * 写入数据
     * @return void
     */
    public static function up()
    {
        Db::connection()->getSchemaBuilder()->create('user_point_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('sys_user_id')->nullable()->comment('管理员ID');
            $table->string('sys_user_name')->nullable()->comment('管理员名称');
            $table->bigInteger('user_id')->nullable()->comment('用户ID');
            $table->string('user_name')->nullable()->comment('会员名称');
            $table->string('operation_stage')->nullable()->comment('变动类型');
            $table->string('description', 500)->nullable()->comment('变动说明');
            $table->decimal('points',20)->nullable()->comment('积分数量');
            $table->decimal('available_points',20)->nullable()->comment('可用积分');
            $table->decimal('frozen_points',20)->nullable()->comment('冻结积分');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->comment('用户积分日志表');
        });
    }

    public static function down()
    {
        Db::connection()->getSchemaBuilder()->dropIfExists('user_point_log');
    }
}