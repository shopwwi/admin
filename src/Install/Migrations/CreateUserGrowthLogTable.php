<?php

namespace Shopwwi\Admin\Install\Migrations;

use Illuminate\Database\Schema\Blueprint;
use support\Db;

class CreateUserGrowthLogTable
{

    /**
     * 写入数据
     * @return void
     */
    public static function up()
    {
        Db::connection()->getSchemaBuilder()->create('user_growth_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('sys_user_id')->nullable()->comment('管理员ID');
            $table->string('sys_user_name')->nullable()->comment('管理员名称');
            $table->bigInteger('user_id')->nullable()->comment('会员编号');
            $table->bigInteger('growth')->nullable()->comment('成长值');
            $table->string('operation_stage')->nullable()->comment('变动类型');
            $table->string('description', 500)->nullable()->comment('操作描述');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->comment('用户成长值日志');
        });
    }

    public static function down()
    {
        Db::connection()->getSchemaBuilder()->dropIfExists('user_growth_log');
    }
}