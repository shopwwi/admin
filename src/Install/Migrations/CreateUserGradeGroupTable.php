<?php

namespace Shopwwi\Admin\Install\Migrations;

use Illuminate\Database\Schema\Blueprint;
use support\Db;

class CreateUserGradeGroupTable
{

    /**
     * 写入数据
     * @return void
     */
    public static function up()
    {
        Db::connection()->getSchemaBuilder()->create('user_grade_group', function (Blueprint $table) {
            $table->increments('id')->comment('用户组');
            $table->string('name', 50)->nullable()->comment('组名称');
            $table->string('type', 10)->nullable()->default('0')->comment('组类别 0普通 1收费 2充值');
            $table->text('rule')->nullable()->comment('购买条件适应非普通组别');
            $table->char('is_default', 1)->nullable()->default('0')->comment('默认组别');
            $table->bigInteger('created_user_id')->nullable()->comment('添加者');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->bigInteger('updated_user_id')->nullable()->comment('修改者');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->softDeletes()->comment('删除标识');
            $table->comment('用户组表');
        });
    }

    public static function down()
    {
        Db::connection()->getSchemaBuilder()->dropIfExists('user_grade_group');
    }
}