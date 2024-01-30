<?php

namespace Shopwwi\Admin\Install\Migrations;

use Illuminate\Database\Schema\Blueprint;
use support\Db;

class CreateSysDictTypeTable
{

    /**
     * 写入数据
     * @return void
     */
    public static function up()
    {
        Db::connection()->getSchemaBuilder()->create('sys_dict_type', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('字典主键');
            $table->string('name', 100)->nullable()->comment('字典名称');
            $table->string('type', 100)->nullable()->unique('type')->comment('字典类型');
            $table->char('status', 1)->nullable()->default('1')->comment('状态（1正常 0停用）');
            $table->char('allow_delete', 1)->nullable()->default('1')->comment('是否可删除1是 0否');
            $table->string('remark', 500)->nullable()->comment('备注');
            $table->bigInteger('created_user_id')->nullable()->comment('添加者');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->bigInteger('updated_user_id')->nullable()->comment('修改者');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->softDeletes()->comment('删除标识');
            $table->comment('系统数据字典类型表');
        });
    }

    public static function down()
    {
        Db::connection()->getSchemaBuilder()->dropIfExists('sys_dict_type');
    }
}