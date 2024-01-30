<?php

namespace Shopwwi\Admin\Install\Migrations;
use Illuminate\Database\Schema\Blueprint;
use support\Db;

class CreateSysShipCompanyTable
{
    /**
     * 写入数据
     * @return void
     */
    public static function up()
    {
        Db::connection()->getSchemaBuilder()->create('sys_ship_company', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('#');
            $table->string('code', 15)->nullable()->comment('物流公司编码');
            $table->string('letter')->nullable()->comment('大写索引');
            $table->string('name')->nullable()->comment('物流名称');
            $table->char('status', 1)->nullable()->default('1')->comment('开启状态 1是 0否');
            $table->char('is_default', 1)->nullable()->default('0')->comment('是否默认 1是 0否');
            $table->string('url')->nullable()->comment('物流公司链接');
            $table->bigInteger('created_user_id')->nullable()->comment('添加者');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->bigInteger('updated_user_id')->nullable()->comment('修改者');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->softDeletes()->comment('删除标识');
            $table->comment('物流公司');
        });
    }
    public static function down()
    {
        Db::connection()->getSchemaBuilder()->dropIfExists('sys_ship_company');
    }
}