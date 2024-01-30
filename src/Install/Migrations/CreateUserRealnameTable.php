<?php

namespace Shopwwi\Admin\Install\Migrations;

use Illuminate\Database\Schema\Blueprint;
use support\Db;

class CreateUserRealnameTable
{

    /**
     * 写入数据
     * @return void
     */
    public static function up()
    {
        Db::connection()->getSchemaBuilder()->create('user_realname', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->nullable()->comment('会员编号');
            $table->string('id_card_name', 50)->nullable()->comment('真实姓名');
            $table->string('id_card_no', 30)->nullable()->comment('身份证号');
            $table->string('id_card_handle')->nullable()->comment('手持身份证照片');
            $table->string('id_card_front')->nullable()->comment('身份证正面照片');
            $table->string('id_card_back')->nullable()->comment('身份证反面照片');
            $table->char('status', 1)->nullable()->default('0')->comment('0 待审核 1已审核 2已拒绝 8解除绑定');
            $table->string('remark')->nullable()->comment('审核意见');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->comment('代码生成表');
        });
    }

    public static function down()
    {
        Db::connection()->getSchemaBuilder()->dropIfExists('user_realname');
    }
}