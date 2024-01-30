<?php

namespace Shopwwi\Admin\Install\Migrations;

use Illuminate\Database\Schema\Blueprint;
use support\Db;

class CreateSysAreaTable
{

    /**
     * 写入数据
     * @return void
     */
    public static function up()
    {
        Db::connection()->getSchemaBuilder()->create('sys_area', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('编号');
            $table->integer('pid')->default(0)->comment('上级编号');
            $table->integer('deep')->default(false)->comment('层级');
            $table->string('name', 32)->comment('名称');
            $table->string('lng', 64)->nullable()->comment('经度');
            $table->string('lat', 64)->nullable()->comment('纬度');
            $table->string('code', 64)->nullable()->comment('电话区号');
            $table->char('initial', 1)->nullable()->comment('大写首字母');
            $table->string('ext_name', 32)->nullable()->comment('全名称');
            $table->string('pinyin')->nullable()->comment('拼音');
            $table->bigInteger('created_user_id')->nullable()->comment('添加者');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->bigInteger('updated_user_id')->nullable()->comment('修改者');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->softDeletes();
            $table->comment('系统地区表');
        });
    }

    public static function down()
    {
        Db::connection()->getSchemaBuilder()->dropIfExists('sys_area');
    }
}