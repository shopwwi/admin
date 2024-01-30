<?php

namespace Shopwwi\Admin\Install\Migrations;

use Illuminate\Database\Schema\Blueprint;
use support\Db;

class CreateSysRoleMenuTable
{

    /**
     * 写入数据
     * @return void
     */
    public static function up()
    {
        Db::connection()->getSchemaBuilder()->create('sys_role_menu', function (Blueprint $table) {
            $table->bigInteger('role_id')->comment('角色ID');
            $table->string('menu_id')->comment('菜单ID');
            $table->primary(['role_id', 'menu_id']);
            $table->comment('系统角色菜单关联表');
        });
    }

    public static function down()
    {
        Db::connection()->getSchemaBuilder()->dropIfExists('sys_role_menu');
    }
}