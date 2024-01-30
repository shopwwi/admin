<?php

namespace Shopwwi\Admin\Install\Migrations;

use Illuminate\Database\Schema\Blueprint;
use support\Db;

class CreateSysArticleClassTable
{

    /**
     * 写入数据
     * @return void
     */
    public static function up()
    {
        Db::connection()->getSchemaBuilder()->create('sys_article_class', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('#');
            $table->integer('sort')->nullable()->default(999)->comment('排序');
            $table->string('title', 20)->nullable()->comment('名称');
            $table->char('type',1)->nullable()->default('0')->comment('文章类型 1-系统内置不可以删除，不可以发布文章 2-系统内置不可以删除，可以发布文章 0-系统内置可以删除，可以发布文章');
            $table->string('position', 5)->nullable()->default('0')->comment('所属位置');
            $table->bigInteger('created_user_id')->nullable()->comment('添加者');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->bigInteger('updated_user_id')->nullable()->comment('修改者');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->softDeletes()->comment('删除标识');
            $table->comment('系统文章分类表');
        });
    }

    public static function down()
    {
        Db::connection()->getSchemaBuilder()->dropIfExists('sys_article_class');
    }
}