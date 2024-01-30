<?php

namespace Shopwwi\Admin\Install\Migrations;

use Illuminate\Database\Schema\Blueprint;
use support\Db;

class CreateUserBalanceRechargeMealTable
{

    /**
     * 写入数据
     * @return void
     */
    public static function up()
    {
        Db::connection()->getSchemaBuilder()->create('user_balance_recharge_meal', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增');
            $table->string('coupon_ids')->nullable()->comment('赠送券');
            $table->decimal('amount', 20)->nullable()->comment('总金额');
            $table->decimal('price', 10)->nullable()->default(0)->comment('售价');
            $table->bigInteger('growth')->nullable()->default(0)->comment('赠送成长值');
            $table->bigInteger('point')->nullable()->default(0)->comment('赠送积分');
            $table->bigInteger('created_user_id')->nullable()->comment('添加者');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->bigInteger('updated_user_id')->nullable()->comment('修改者');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->softDeletes()->comment('删除标识');
            $table->comment('用户充值套餐表');
        });
    }

    public static function down()
    {
        Db::connection()->getSchemaBuilder()->dropIfExists('user_balance_recharge_meal');
    }
}