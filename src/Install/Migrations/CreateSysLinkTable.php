<?php

namespace Shopwwi\Admin\Install\Migrations;

use Illuminate\Database\Schema\Blueprint;
use support\Db;

class CreateSysLinkTable
{

    /**
     * 写入数据
     * @return void
     */
    public static function up()
    {
        Db::connection()->getSchemaBuilder()->create('sys_link', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable()->comment('链接名称');
            $table->string('url')->nullable()->comment('链接地址');
            $table->string('image')->nullable()->comment('链接图片');
            $table->integer('sort')->nullable()->default(999)->comment('排序');
            $table->char('status', 1)->nullable()->default('0')->comment('状态 1显示 0隐藏');
            $table->char('is_blank', 1)->nullable()->default('0')->comment('是否新窗口');
            $table->string('app')->nullable()->comment('所属应用');
            $table->bigInteger('created_user_id')->nullable()->comment('添加者');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->bigInteger('updated_user_id')->nullable()->comment('修改者');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->softDeletes()->comment('删除标识');
            $table->comment('系统友情链接表');
        });
    }

    public static function down()
    {
        Db::connection()->getSchemaBuilder()->dropIfExists('sys_link');
    }
}