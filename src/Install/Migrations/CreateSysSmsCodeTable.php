<?php

namespace Shopwwi\Admin\Install\Migrations;

use Illuminate\Database\Schema\Blueprint;
use support\Db;

class CreateSysSmsCodeTable
{

    /**
     * 写入数据
     * @return void
     */
    public static function up()
    {
        Db::connection()->getSchemaBuilder()->create('sys_sms_code', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('auth_code', 50)->nullable()->comment('短信动态码');
            $table->string('content', 200)->nullable()->comment('短信内容');
            $table->string('ip', 50)->nullable()->comment('请求IP');
            $table->string('mobile_phone', 11)->nullable()->comment('接收手机号');
            $table->string('send_type', 11)->nullable()->comment('短信类型');
            $table->char('status', 1)->nullable()->default('0')->comment('使用状态 0为未使用，1为已使用');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->comment('修改时间');
            $table->comment('系统短信验证码表');
        });
    }

    public static function down()
    {
        Db::connection()->getSchemaBuilder()->dropIfExists('sys_sms_code');
    }
}