<?php

namespace Shopwwi\Admin\Install\Migrations;

use Illuminate\Database\Schema\Blueprint;
use support\Db;

class CreateGenTableColumnTable
{

    /**
     * 写入数据
     * @return void
     */
    public static function up()
    {
        Db::connection()->getSchemaBuilder()->create('gen_table_column', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('编号');
            $table->string('table_id', 64)->nullable()->comment('归属表编号');
            $table->string('name', 200)->nullable()->comment('列名称');
            $table->string('comment', 500)->nullable()->comment('列描述');
            $table->string('column_type', 100)->nullable()->comment('列类型');
            $table->char('is_pk',1)->nullable()->default('0')->comment('是否主键（1是 0否）');
            $table->char('is_increment',1)->nullable()->default('0')->comment('是否自增（1是 0否）');
            $table->char('is_required',1)->nullable()->default('1')->comment('是否必填（1是 0否）');
            $table->char('is_insert',1)->nullable()->default('1')->comment('是否为插入字段（1是 0否）');
            $table->char('is_edit',1)->nullable()->default('1')->comment('是否编辑字段（1是 0否）');
            $table->char('is_list',1)->nullable()->default('1')->comment('是否列表字段（1是 0否）');
            $table->char('is_query',1)->nullable()->default('1')->comment('是否查询字段（1是 0否）');
            $table->char('is_show',1)->nullable()->default('1')->comment('是否详情字段（1是 0否）');
            $table->string('query_type', 200)->nullable()->default('EQ')->comment('查询方式（等于、不等于、大于、小于、范围）');
            $table->string('html_type', 200)->nullable()->comment('显示类型（文本框、文本域、下拉框、复选框、单选框、日期控件）');
            $table->string('dict_type', 200)->nullable()->default('')->comment('字典类型');
            $table->integer('sort')->nullable()->comment('排序');
            $table->bigInteger('created_user_id')->nullable()->comment('添加者');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->bigInteger('updated_user_id')->nullable()->comment('修改者');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->softDeletes()->comment('删除标识');
            $table->comment('代码生成项目表');
        });
    }

    public static function down()
    {
        Db::connection()->getSchemaBuilder()->dropIfExists('gen_table_column');
    }
}