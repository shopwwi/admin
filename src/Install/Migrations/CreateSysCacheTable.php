<?php

namespace Shopwwi\Admin\Install\Migrations;

use Illuminate\Database\Schema\Blueprint;
use support\Db;

class CreateSysCacheTable
{

    /**
     * 写入数据
     * @return void
     */
    public static function up()
    {
        Db::connection()->getSchemaBuilder()->create('sys_cache', function (Blueprint $table) {
            $table->string('key', 50)->primary()->comment('唯一值');
            $table->string('name')->nullable()->comment('缓存名称');
            $table->string('desc')->nullable()->comment('缓存介绍');
            $table->text('model')->nullable()->comment('缓存处理器多个以逗号分割');
            $table->timestamp('operation_time')->nullable()->comment('操作时间');
            $table->comment('缓存操作表');
        });
    }

    public static function down()
    {
        Db::connection()->getSchemaBuilder()->dropIfExists('sys_cache');
    }
}