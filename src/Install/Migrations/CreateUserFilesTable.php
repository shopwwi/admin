<?php

namespace Shopwwi\Admin\Install\Migrations;

use Illuminate\Database\Schema\Blueprint;
use support\Db;

class CreateUserFilesTable
{

    /**
     * 写入数据
     * @return void
     */
    public static function up()
    {
        Db::connection()->getSchemaBuilder()->create('user_files', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('#');
            $table->string('name')->nullable()->comment('文件名称');
            $table->integer('width')->nullable()->comment('宽度');
            $table->integer('height')->nullable()->comment('高度');
            $table->integer('size')->nullable()->comment('文件大小');
            $table->string('files_type')->nullable()->comment('文件类型');
            $table->string('path')->nullable()->comment('文件路径');
            $table->string('original_name')->nullable()->comment('文件原名');
            $table->integer('user_id')->nullable()->comment('会员编号');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->comment('用户地址表');
        });
    }

    public static function down()
    {
        Db::connection()->getSchemaBuilder()->dropIfExists('user_files');
    }
}