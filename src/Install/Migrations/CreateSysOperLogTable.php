<?php

namespace Shopwwi\Admin\Install\Migrations;

use Illuminate\Database\Schema\Blueprint;
use support\Db;

class CreateSysOperLogTable
{

    /**
     * 写入数据
     * @return void
     */
    public static function up()
    {
        Db::connection()->getSchemaBuilder()->create('sys_oper_log', function (Blueprint $table) {
            $table->bigInteger('id', true)->comment('日志主键');
            $table->string('title', 50)->nullable()->comment('模块标题');
            $table->char('business_type', 1)->nullable()->default('O')->comment('业务类型（O其它 C新增 E修改 D删除 H恢复）');
            $table->string('method', 100)->nullable()->comment('方法名称');
            $table->string('request_method', 10)->nullable()->default('')->comment('请求方式');
            $table->char('type', 1)->nullable()->default('O')->comment('操作类别（O其它 S后台用户 M手机端用户）');
            $table->string('name', 50)->nullable()->comment('操作人员');
            $table->string('url')->nullable()->comment('请求URL');
            $table->string('ip', 50)->nullable()->default('')->comment('主机地址');
            $table->string('location')->nullable()->comment('操作地点');
            $table->longText('param')->nullable()->comment('请求参数');
            $table->longText('json_result')->nullable()->comment('返回参数');
            $table->char('status', 1)->nullable()->default('1')->comment('操作状态（1正常 0异常）');
            $table->longText('error_msg')->nullable()->comment('错误消息');
            $table->timestamp('created_at')->nullable()->comment('操作时间');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->comment('系统操作日志表');
        });
    }

    public static function down()
    {
        Db::connection()->getSchemaBuilder()->dropIfExists('sys_oper_log');
    }
}