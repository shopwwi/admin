<?php

namespace Shopwwi\Admin\Install\Migrations;

use Illuminate\Database\Schema\Blueprint;
use support\Db;

class CreateUserLabelTable
{

    /**
     * 写入数据
     * @return void
     */
    public static function up()
    {
        Db::connection()->getSchemaBuilder()->create('user_label', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable()->comment('标签名称');
            $table->integer('sort')->nullable()->comment('排序');
            $table->string('remark')->nullable()->comment('备注');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->comment('用户标签表');
        });
    }

    public static function down()
    {
        Db::connection()->getSchemaBuilder()->dropIfExists('user_label');
    }
}