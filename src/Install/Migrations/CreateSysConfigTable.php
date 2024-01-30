<?php

namespace Shopwwi\Admin\Install\Migrations;

use Illuminate\Database\Schema\Blueprint;
use support\Db;

class CreateSysConfigTable
{

    /**
     * 写入数据
     * @return void
     */
    public static function up()
    {
        Db::connection()->getSchemaBuilder()->create('sys_config', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('参数主键');
            $table->string('name', 100)->nullable()->default('')->comment('参数名称');
            $table->string('key', 100)->nullable()->default('')->unique('key')->comment('参数键名');
            $table->longText('value')->nullable()->comment('参数键值');
            $table->char('is_system', 1)->nullable()->default('0')->comment('系统内置（1是 0否）');
            $table->char('is_open', 1)->nullable()->default('0')->comment('开放数据（1是 0否）私密配置记得关闭');
            $table->string('remark', 500)->nullable()->comment('备注');
            $table->bigInteger('created_user_id')->nullable()->comment('添加者');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->bigInteger('updated_user_id')->nullable()->comment('修改者');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->softDeletes()->comment('删除标识');
            $table->comment('系统配置表');
        });
    }

    public static function down()
    {
        Db::connection()->getSchemaBuilder()->dropIfExists('sys_config');
    }
}