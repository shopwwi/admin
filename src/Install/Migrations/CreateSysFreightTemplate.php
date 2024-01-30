<?php

namespace Shopwwi\Admin\Install\Migrations;

use Illuminate\Database\Schema\Blueprint;
use support\Db;

class CreateSysFreightTemplate
{

    /**
     * 写入数据
     * @return void
     */
    public static function up()
    {
        Db::connection()->getSchemaBuilder()->create('sys_freight_template', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增编号');
            $table->string('title', 200)->nullable()->comment('模板名称');
            $table->bigInteger('store_id')->nullable()->default(0)->comment('店铺编号');
            $table->text('area_id')->nullable()->comment('地区编号集合');
            $table->string('calc_type', 100)->nullable()->comment('计价方式');
            $table->char('freight_free', 1)->nullable()->default('0')->comment('是否免费（1是 0否）');
            $table->bigInteger('created_user_id')->nullable()->comment('添加者');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->bigInteger('updated_user_id')->nullable()->comment('修改者');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->softDeletes()->comment('删除标识');
            $table->comment('系统运费模板表');
        });
    }

    public static function down()
    {
        Db::connection()->getSchemaBuilder()->dropIfExists('sys_freight_template');
    }
}