<?php

namespace Shopwwi\Admin\Install\Migrations;

use Illuminate\Database\Schema\Blueprint;
use support\Db;

class CreateSysMenuTable
{

    /**
     * 写入数据
     * @return void
     */
    public static function up()
    {
        Db::connection()->getSchemaBuilder()->create('sys_menu', function (Blueprint $table) {
            $table->increments('id')->comment('菜单ID');
            $table->string('name', 50)->comment('菜单名称');
            $table->string('pid')->nullable()->nullable()->comment('父菜单ID');
            $table->integer('sort')->nullable()->default(999)->comment('显示顺序');
            $table->string('path', 200)->nullable()->default('')->comment('路由地址');
            $table->string('component')->nullable()->comment('组件路径');
            $table->string('key')->nullable()->comment('菜单唯一标识');
            $table->char('is_frame', 1)->nullable()->default('0')->comment('是否为外链（1是 0否）');
            $table->char('is_cache', 1)->nullable()->default('0')->comment('是否缓存（1缓存 0不缓存）');
            $table->char('menu_type', 1)->nullable()->default('M')->comment('菜单类型（M目录 C菜单 F按钮）');
            $table->char('visible', 1)->nullable()->default('1')->comment('菜单状态（1显示 0隐藏）');
            $table->char('status', 1)->nullable()->default('1')->comment('菜单状态（1正常 0停用）');
            $table->text('perms')->nullable()->comment('权限标识');
            $table->string('icon', 100)->nullable()->comment('菜单图标');
            $table->bigInteger('created_user_id')->nullable()->comment('创建者');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->bigInteger('updated_user_id')->nullable()->comment('更新者');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->string('remark', 500)->nullable()->default('')->comment('备注');
            $table->comment('系统菜单表');
        });
    }

    public static function down()
    {
        Db::connection()->getSchemaBuilder()->dropIfExists('sys_menu');
    }
}