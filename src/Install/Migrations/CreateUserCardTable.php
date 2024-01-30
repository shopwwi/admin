<?php

namespace Shopwwi\Admin\Install\Migrations;

use Illuminate\Database\Schema\Blueprint;
use support\Db;

class CreateUserCardTable
{

    /**
     * 写入数据
     * @return void
     */
    public static function up()
    {
        Db::connection()->getSchemaBuilder()->create('user_card', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->nullable()->comment('会员编号');
            $table->string('bank_name')->nullable()->comment('银行名称');
            $table->string('bank_account')->nullable()->comment('银行账号');
            $table->string('bank_username')->nullable()->comment('持卡人');
            $table->string('bank_branch')->nullable()->comment('银行支行');
            $table->string('bank_type')->nullable()->comment('类型 BANK银行 ALIPAY支付宝 WECHAT 微信');
            $table->string('mobile', 11)->nullable()->comment('预留电话');
            $table->char('status', 1)->nullable()->default('0')->comment('状态 1正常 0禁用');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->comment('用户银行卡表');
        });
    }

    public static function down()
    {
        Db::connection()->getSchemaBuilder()->dropIfExists('user_card');
    }
}