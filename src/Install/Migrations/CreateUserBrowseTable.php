<?php

namespace Shopwwi\Admin\Install\Migrations;

use Illuminate\Database\Schema\Blueprint;
use support\Db;

class CreateUserBrowseTable
{

    /**
     * 写入数据
     * @return void
     */
    public static function up()
    {
        Db::connection()->getSchemaBuilder()->create('user_browse', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('type')->comment('访问类型');
            $table->integer('item_id')->nullable()->comment('访问类型对应编号');
            $table->text('attached')->nullable()->comment('附加内容');
            $table->timestamp('created_at')->nullable()->comment('添加时间');
            $table->timestamp('updated_at')->nullable()->comment('修改时间');
            $table->comment('用户访问记录');
        });
    }

    public static function down()
    {
        Db::connection()->getSchemaBuilder()->dropIfExists('user_browse');
    }
}