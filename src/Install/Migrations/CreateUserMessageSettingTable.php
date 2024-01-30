<?php

namespace Shopwwi\Admin\Install\Migrations;

use Illuminate\Database\Schema\Blueprint;
use support\Db;

class CreateUserMessageSettingTable
{

    /**
     * 写入数据
     * @return void
     */
    public static function up()
    {
        Db::connection()->getSchemaBuilder()->create('user_message_setting', function (Blueprint $table) {
            $table->string('tpl_code')->comment('模板编号');
            $table->bigInteger('user_id')->comment('会员编号');
            $table->char('status', 1)->nullable()->default('1')->comment('是否接收');
            $table->primary(['tpl_code', 'user_id']);
            $table->comment('用户消息接收设置表');
        });
    }

    public static function down()
    {
        Db::connection()->getSchemaBuilder()->dropIfExists('user_message_setting');
    }
}