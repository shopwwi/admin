<?php

namespace Shopwwi\Admin\Install\Migrations;

use Illuminate\Database\Schema\Blueprint;
use support\Db;

class CreateSysArticleTable
{

    /**
     * 写入数据
     * @return void
     */
    public static function up()
    {
        Db::connection()->getSchemaBuilder()->create('sys_article', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('索引');
            $table->bigInteger('category_id')->comment('分类ID');
            $table->string('image')->nullable()->comment('封面主图');
            $table->longText('content')->nullable()->comment('文章内容');
            $table->integer('sort')->nullable()->default(999)->comment('排序');
            $table->string('title', 100)->nullable()->comment('文章标题');
            $table->string('url')->nullable()->comment('文章链接');
            $table->char('allow_delete', 1)->nullable()->default('0')->comment('是否可删除 1是0否');
            $table->bigInteger('created_user_id')->nullable()->comment('添加者');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->bigInteger('updated_user_id')->nullable()->comment('修改者');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->softDeletes()->comment('删除标识');
            $table->comment('系统文章表');
        });
    }

    public static function down()
    {
        Db::connection()->getSchemaBuilder()->dropIfExists('sys_article');
    }
}