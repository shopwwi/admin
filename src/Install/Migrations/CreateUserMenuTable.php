<?php

namespace Shopwwi\Admin\Install\Migrations;

use Illuminate\Database\Schema\Blueprint;
use support\Db;

class CreateUserMenuTable
{

    /**
     * 写入数据
     * @return void
     */
    public static function up()
    {
        Db::connection()->getSchemaBuilder()->create('user_menu', function (Blueprint $table) {
            $table->string('id')->comment('菜单ID');
            $table->string('name', 50)->comment('菜单名称');
            $table->string('pid')->nullable()->comment('父菜单ID');
            $table->integer('sort')->nullable()->default(999)->comment('显示顺序');
            $table->string('path', 200)->nullable()->default('')->comment('路由地址');
            $table->string('component')->nullable()->comment('组件路径');
            $table->char('is_frame', 1)->nullable()->default('0')->comment('是否为外链（1是 0否）');
            $table->char('menu_type', 1)->nullable()->default('M')->comment('菜单类型（M目录 C菜单）');
            $table->char('status', 1)->nullable()->default('1')->comment('菜单状态（1正常 0停用）');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->comment('会员中心菜单表');
        });
    }

    public static function down()
    {
        Db::connection()->getSchemaBuilder()->dropIfExists('user_menu');
    }
}