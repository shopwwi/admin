<?php

namespace Shopwwi\Admin\Install\Migrations;

use Illuminate\Database\Schema\Blueprint;
use support\Db;

class CreateSysAlbumFilesTable
{

    /**
     * 写入数据
     * @return void
     */
    public static function up()
    {
        Db::connection()->getSchemaBuilder()->create('sys_album_files', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('#');
            $table->integer('album_id')->nullable()->comment('相册类型');
            $table->bigInteger('height')->nullable()->comment('文件高度');
            $table->string('name')->nullable()->comment('文件名称');
            $table->bigInteger('size')->nullable()->comment('文件大小');
            $table->string('files_type', 50)->nullable()->comment('文件类型 image图片 video视频');
            $table->integer('width')->nullable()->comment('文件宽度');
            $table->string('original_name')->nullable()->comment('文件原名');
            $table->bigInteger('created_user_id')->nullable()->comment('添加者');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->bigInteger('updated_user_id')->nullable()->comment('修改者');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->softDeletes()->comment('删除标识');
            $table->comment('系统文件表');
        });
    }

    public static function down()
    {
        Db::connection()->getSchemaBuilder()->dropIfExists('sys_album_files');
    }
}