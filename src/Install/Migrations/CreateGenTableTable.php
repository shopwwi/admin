<?php

namespace Shopwwi\Admin\Install\Migrations;

use Illuminate\Database\Schema\Blueprint;
use support\Db;

class CreateGenTableTable
{

    /**
     * 写入数据
     * @return void
     */
    public static function up()
    {
        Db::connection()->getSchemaBuilder()->create('gen_table', function (Blueprint $table) {
            $table->bigInteger('id', true)->comment('编号');
            $table->string('name', 200)->nullable()->default('')->comment('表名称');
            $table->string('comment', 500)->nullable()->default('')->comment('表描述');
            $table->string('class_name', 100)->nullable()->default('')->comment('实体类名称');
            $table->string('tpl_category', 200)->nullable()->default('crud')->comment('使用的模板（crud单表操作 tree树表操作）');
            $table->string('package_name', 100)->nullable()->comment('生成包路径');
            $table->string('module_name')->nullable()->comment('生成模块名');
            $table->string('business_name')->nullable()->comment('生成业务名');
            $table->string('function_name')->nullable()->comment('生成功能名');
            $table->string('function_author')->nullable()->comment('生成功能作者');
            $table->char('gen_type', 1)->nullable()->default('0')->comment('生成代码方式（0zip压缩包 1自定义路径）');
            $table->string('gen_path', 200)->nullable()->default('/')->comment('生成路径（不填默认项目路径）');
            $table->string('options', 1000)->nullable()->comment('其它生成选项');
            $table->string('remark', 500)->nullable()->comment('备注');
            $table->bigInteger('created_user_id')->nullable()->comment('添加者');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->bigInteger('updated_user_id')->nullable()->comment('修改者');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->softDeletes()->comment('删除标识');
            $table->comment('代码生成表');
        });
    }

    public static function down()
    {
        Db::connection()->getSchemaBuilder()->dropIfExists('gen_table');
    }
}