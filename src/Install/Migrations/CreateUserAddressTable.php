<?php

namespace Shopwwi\Admin\Install\Migrations;

use Illuminate\Database\Schema\Blueprint;
use support\Db;

class CreateUserAddressTable
{

    /**
     * 写入数据
     * @return void
     */
    public static function up()
    {
        Db::connection()->getSchemaBuilder()->create('user_address', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('#');
            $table->string('area_info')->nullable()->comment('地址');
            $table->integer('area_id')->nullable()->comment('地区编号');
            $table->integer('area_ids')->nullable()->comment('地区编号集合');
            $table->string('address_info')->nullable()->comment('地址详情');
            $table->char('address_default', 1)->nullable()->default('0')->comment('是否默认（1是0否）');
            $table->bigInteger('user_id')->nullable()->comment('用户ID');
            $table->string('mobile')->nullable()->comment('手机号');
            $table->string('real_name')->nullable()->comment('真实姓名');
            $table->string('telphone')->nullable()->comment('电话');
            $table->string('lat')->nullable();
            $table->string('lng')->nullable();
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->comment('用户地址表');
        });
    }

    public static function down()
    {
        Db::connection()->getSchemaBuilder()->dropIfExists('user_address');
    }
}