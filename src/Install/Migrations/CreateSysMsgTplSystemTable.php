<?php

namespace Shopwwi\Admin\Install\Migrations;

use Illuminate\Database\Schema\Blueprint;
use support\Db;

class CreateSysMsgTplSystemTable
{

    /**
     * 写入数据
     * @return void
     */
    public static function up()
    {
        Db::connection()->getSchemaBuilder()->create('sys_msg_tpl_system', function (Blueprint $table) {
            $table->string('code')->primary();
            $table->string('send_type', 10)->nullable()->default('MSG')->comment('模板类型
MSG 短信，EMAIL 邮件');
            $table->string('content', 10000)->nullable()->comment('内容');
            $table->string('name')->nullable()->comment('消息模板名称');
            $table->string('title', 500)->nullable()->comment('标题');
            $table->bigInteger('created_user_id')->nullable()->comment('添加者');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->bigInteger('updated_user_id')->nullable()->comment('修改者');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->softDeletes()->comment('删除标识');
            $table->comment('系统消息模板表');
        });
    }

    public static function down()
    {
        Db::connection()->getSchemaBuilder()->dropIfExists('sys_msg_tpl_system');
    }
}