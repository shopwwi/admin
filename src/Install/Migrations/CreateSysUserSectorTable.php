<?php

namespace Shopwwi\Admin\Install\Migrations;

use Illuminate\Database\Schema\Blueprint;
use support\Db;

class CreateSysUserSectorTable
{

    /**
     * 写入数据
     * @return void
     */
    public static function up()
    {
        Db::connection()->getSchemaBuilder()->create('sys_user_sector', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('部门ID');
            $table->string('name', 30)->comment('部门名称');
            $table->integer('pid')->comment('上级部门');
            $table->integer('sort')->comment('显示顺序');
            $table->integer('leader')->comment('负责人');
            $table->char('status', 1)->default('1')->comment('部门状态（1正常 0停用）');
            $table->bigInteger('created_user_id')->nullable()->default(0)->comment('创建编号');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->bigInteger('updated_user_id')->nullable()->default(0)->comment('更新者编号');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->softDeletes()->comment('删除标志');
            $table->comment('系统部门表');
        });
    }

    public static function down()
    {
        Db::connection()->getSchemaBuilder()->dropIfExists('sys_user_sector');
    }
}