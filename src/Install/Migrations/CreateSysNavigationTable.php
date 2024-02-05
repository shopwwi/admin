<?php

namespace Shopwwi\Admin\Install\Migrations;

use Illuminate\Database\Schema\Blueprint;
use support\Db;

class CreateSysNavigationTable
{

    /**
     * 写入数据
     * @return void
     */
    public static function up()
    {
        Db::connection()->getSchemaBuilder()->create('sys_navigation', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable()->comment('导航名称');
            $table->text('link')->nullable()->comment('链接类型');
            $table->string('icon')->nullable()->comment('导航图标');
            $table->string('image')->nullable()->comment('导航图片');
            $table->char('status', 1)->nullable()->default('0')->comment('状态 1显示 0隐藏');
            $table->char('is_blank', 1)->nullable()->default('0')->comment('是否新窗口');
            $table->string('position',10)->nullable()->comment('展示位置');
            $table->string('code')->nullable()->comment('高亮码');
            $table->string('app')->nullable()->comment('所属应用');
            $table->integer('sort')->nullable()->default(999)->comment('显示顺序');
            $table->bigInteger('created_user_id')->nullable()->comment('添加者');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->bigInteger('updated_user_id')->nullable()->comment('修改者');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->comment('系统导航表');
        });
    }

    public static function down()
    {
        Db::connection()->getSchemaBuilder()->dropIfExists('sys_navigation');
    }
}