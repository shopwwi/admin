<?php

namespace Shopwwi\Admin\Install\Migrations;

use Illuminate\Database\Schema\Blueprint;
use support\Db;

class CreateSysEmailCodeTable
{

    /**
     * 写入数据
     * @return void
     */
    public static function up()
    {
        Db::connection()->getSchemaBuilder()->create('sys_email_code', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('auth_code', 50)->nullable()->comment('动态码');
            $table->string('content', 1000)->nullable()->comment('邮件内容');
            $table->string('email', 100)->nullable()->comment('接收邮件');
            $table->string('ip', 50)->nullable()->comment('请求IP');
            $table->string('send_type', 11)->nullable()->comment('邮件类型');
            $table->char('status', 1)->nullable()->default('0')->comment('使用状态 0为未使用，1为已使用');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->comment('修改时间');
            $table->comment('系统接收邮件验证表');
        });
    }

    public static function down()
    {
        Db::connection()->getSchemaBuilder()->dropIfExists('sys_email_code');
    }
}