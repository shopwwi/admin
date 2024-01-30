<?php

namespace Shopwwi\Admin\Install\Migrations;

use Illuminate\Database\Schema\Blueprint;
use support\Db;

class CreateSysRoleTable
{

    /**
     * 写入数据
     * @return void
     */
    public static function up()
    {
        Db::connection()->getSchemaBuilder()->create('sys_role', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('角色ID');
            $table->string('name', 30)->comment('角色名称');
            $table->string('key', 100)->nullable()->comment('角色权限字符串');
            $table->integer('sort')->comment('显示顺序');
            $table->char('status', 1)->default('1')->comment('角色状态（1正常 0停用）');
            $table->char('scope',1)->default('0')->comment('数据权限（0：全部数据权限 1：自定数据权限 2：本部门数据权限 3：本部门及以下数据权限）');
            $table->string('remark', 500)->nullable()->comment('备注');
            $table->bigInteger('created_user_id')->nullable()->default(0)->comment('创建编号');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->bigInteger('updated_user_id')->nullable()->default(0)->comment('更新者编号');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->softDeletes()->comment('删除标志');
            $table->comment('系统角色表');
        });
    }

    public static function down()
    {
        Db::connection()->getSchemaBuilder()->dropIfExists('sys_role');
    }
}