<?php

namespace Shopwwi\Admin\Install\Migrations;

use Illuminate\Database\Schema\Blueprint;
use support\Db;

class CreateSysFreightArea
{

    /**
     * 写入数据
     * @return void
     */
    public static function up()
    {
        Db::connection()->getSchemaBuilder()->create('sys_freight_area', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('字典编码');
            $table->longText('area_id')->nullable()->comment('地区编号');
            $table->longText('area_name')->nullable()->comment('地区名称');
            $table->integer('freight_id')->nullable()->default(0)->comment('模板编号');
            $table->decimal('item1',20)->nullable()->default(0)->comment('首');
            $table->decimal('item2',20)->nullable()->default(0)->comment('续');
            $table->decimal('price1',20)->nullable()->default(0)->comment('首价');
            $table->decimal('price2',20)->nullable()->default(0)->comment('续价');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->comment('系统运费模板项目表');
        });
    }

    public static function down()
    {
        Db::connection()->getSchemaBuilder()->dropIfExists('sys_freight_area');
    }
}