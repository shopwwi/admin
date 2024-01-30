<?php

namespace Shopwwi\Admin\Install\Migrations;

use Illuminate\Database\Schema\Blueprint;
use support\Db;

class CreateUserOpensTable
{

    /**
     * 写入数据
     * @return void
     */
    public static function up()
    {
        Db::connection()->getSchemaBuilder()->create('user_opens', function (Blueprint $table) {
            $table->bigInteger('user_id')->comment('会员ID');
            $table->string('open_type')->default('QQ')->comment('关联类型QQ WECHAT SINA');
            $table->string('open_id')->nullable()->comment('开放ID');
            $table->text('open_info')->nullable()->comment('用户信息');
            $table->string('open_unionid')->nullable()->comment('开放公用ID');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->primary(['user_id', 'open_type']);
            $table->comment('用户第三方关联表');
        });
    }

    public static function down()
    {
        Db::connection()->getSchemaBuilder()->dropIfExists('user_opens');
    }
}