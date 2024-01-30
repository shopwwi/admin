<?php

namespace Shopwwi\Admin\Install\Migrations;

use Illuminate\Database\Schema\Blueprint;
use support\Db;

class CreateUserGradeTable
{

    /**
     * 写入数据
     * @return void
     */
    public static function up()
    {
        Db::connection()->getSchemaBuilder()->create('user_grade', function (Blueprint $table) {
            $table->increments('id', true);
            $table->integer('group_id')->nullable()->comment('组ID');
            $table->integer('level')->nullable()->comment('数字等级');
            $table->string('name')->nullable()->comment('等级名称');
            $table->string('ext_name')->nullable()->comment('等级个性化名称');
            $table->text('rule')->nullable()->comment('升级条件json');
            $table->string('icon')->nullable()->comment('等级图标');
            $table->string('image_name')->nullable()->comment('等级背景');
            $table->text('remark')->nullable()->comment('等级说明');
            $table->char('status', 1)->nullable()->default('1')->comment('是否开启 1开启 0关闭');
            $table->char('is_default', 1)->nullable()->default('0')->comment('默认等级');
            $table->bigInteger('created_user_id')->nullable()->comment('添加者');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->bigInteger('updated_user_id')->nullable()->comment('修改者');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->softDeletes()->comment('删除标识');
            $table->comment('用户等级表');
        });
    }

    public static function down()
    {
        Db::connection()->getSchemaBuilder()->dropIfExists('user_grade');
    }
}