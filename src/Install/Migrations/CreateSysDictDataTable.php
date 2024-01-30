<?php

namespace Shopwwi\Admin\Install\Migrations;

use Illuminate\Database\Schema\Blueprint;
use support\Db;

class CreateSysDictDataTable
{

    /**
     * 写入数据
     * @return void
     */
    public static function up()
    {
        Db::connection()->getSchemaBuilder()->create('sys_dict_data', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('字典编码');
            $table->integer('sort')->nullable()->default(999)->comment('字典排序');
            $table->string('label', 100)->nullable()->default('')->comment('字典标签');
            $table->string('value', 100)->nullable()->default('')->comment('字典键值');
            $table->string('type', 100)->nullable()->default('')->comment('字典类型');
            $table->string('css_class', 100)->nullable()->comment('样式属性（其他样式扩展）');
            $table->string('list_class', 100)->nullable()->comment('表格回显样式');
            $table->char('is_default', 1)->nullable()->default('0')->comment('是否默认（1是 0否）');
            $table->char('status', 1)->nullable()->default('1')->comment('状态（1正常 0停用）');
            $table->char('allow_delete', 1)->nullable()->default('1')->comment('是否可删除1是 0否');
            $table->string('remark', 500)->nullable()->comment('备注');
            $table->bigInteger('created_user_id')->nullable()->comment('添加者');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->bigInteger('updated_user_id')->nullable()->comment('修改者');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->softDeletes()->comment('删除标识');
            $table->comment('系统数据字典数据表');
        });
    }

    public static function down()
    {
        Db::connection()->getSchemaBuilder()->dropIfExists('sys_dict_data');
    }
}