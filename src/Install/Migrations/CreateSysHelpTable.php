<?php

namespace Shopwwi\Admin\Install\Migrations;

use Illuminate\Database\Schema\Blueprint;
use support\Db;

class CreateSysHelpTable
{

    /**
     * 写入数据
     * @return void
     */
    public static function up()
    {
        Db::connection()->getSchemaBuilder()->create('sys_help', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('title')->comment('标题');
            $table->string('url')->nullable()->comment('链接地址');
            $table->text('content')->nullable()->comment('内容');
            $table->integer('class_id')->nullable()->comment('分类编号');
            $table->integer('sort')->default(999)->comment('排序');
            $table->bigInteger('created_user_id')->nullable()->comment('添加者');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->bigInteger('updated_user_id')->nullable()->comment('修改者');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->softDeletes()->comment('删除时间');
            $table->comment('系统帮助表');
        });
    }

    public static function down()
    {
        Db::connection()->getSchemaBuilder()->dropIfExists('sys_help');
    }
}